<?php

namespace App\Http\Controllers;

use App\Models\SyncToken;
use App\Models\SyncLog;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Product;
use App\Variation;
use App\Transaction;
use App\TransactionSellLine;
use App\TransactionPayment;
use App\TaxRate;
use App\Category;
use App\Brand;
use App\Unit;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Two-way Cloud Sync Controller
 * ─────────────────────────────
 *
 * ENDPOINTS
 *  POST  /sync/register    – register a new offline device → returns token
 *  POST  /sync/pull        – server → device  (download changes since last pull)
 *  POST  /sync/push        – device → server  (upload changes made offline)
 *  GET   /sync/status      – heartbeat + token info
 *  GET   /sync             – sync dashboard view (requires auth)
 *  DELETE /sync/token/{id} – revoke a device token (requires auth)
 */
class CloudSyncController extends Controller
{
    // ── CORS headers added to every JSON response ────────────────────────────
    // Allows Laragon (or any trusted local dev instance) to call the live server.
    private function syncResponse(array $data, int $status = 200)
    {
        $origin = request()->header('Origin', '*');

        // Whitelist: same origin, localhost variants, and configured SYNC_ALLOWED_ORIGINS
        $allowed = array_filter(array_merge(
            ['http://localhost', 'http://127.0.0.1', 'http://reenson.test'],
            explode(',', env('SYNC_ALLOWED_ORIGINS', ''))
        ));

        $allowOrigin = in_array($origin, $allowed) ? $origin : '*';

        return response()->json($data, $status)->withHeaders([
            'Access-Control-Allow-Origin'  => $allowOrigin,
            'Access-Control-Allow-Headers' => 'Content-Type, X-Sync-Token, X-CSRF-TOKEN, Accept',
            'Access-Control-Allow-Methods' => 'GET, POST, DELETE, OPTIONS',
        ]);
    }

    // Handle OPTIONS pre-flight for cross-domain requests
    public function preflight()
    {
        return $this->syncResponse([], 204);
    }

    // ── Device Registration ──────────────────────────────────────────────────

    /**
     * Register an offline device and return its unique sync token.
     * This is called once when the user enables offline mode.
     */
    public function register(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:business,id',
            'device_name' => 'nullable|string|max:100',
            'device_type' => 'nullable|in:browser,desktop,mobile',
        ]);

        $user = auth()->user();
        $businessId = $request->input('business_id');

        // Allow only users belonging to this business (or superadmin)
        if ($user && $user->business_id !== $businessId && $user->username !== 'saas_admin') {
            return $this->syncResponse(['error' => 'Unauthorized'], 403);
        }

        $token = SyncToken::create([
            'business_id' => $businessId,
            'user_id'     => optional($user)->id,
            'token'       => SyncToken::generate(),
            'device_name' => $request->input('device_name', 'Unnamed Device'),
            'device_type' => $request->input('device_type', 'browser'),
            'is_active'   => true,
        ]);

        return $this->syncResponse([
            'sync_token'     => $token->token,
            'device_id'      => $token->id,
            'message'        => 'Device registered. Use sync_token for all future sync requests.',
        ]);
    }

    // ── Pull: Server → Device ────────────────────────────────────────────────

    /**
     * Return everything that changed since the device's last pull.
     * The device sends its token; server returns delta data.
     */
    public function pull(Request $request)
    {
        try {
            [$token, $errorResponse] = $this->resolveToken($request);
            if ($errorResponse) return $errorResponse;
        } catch (\Throwable $e) {
            return $this->syncResponse([
                'error' => 'Sync tables not ready. Please run: php artisan migrate --force on the server. (' . $e->getMessage() . ')',
            ], 503);
        }

        $since      = $token->last_pulled_at;     // null = full sync
        $businessId = $token->business_id;
        $pulledAt   = now();

        // Pull each dataset individually so one failure doesn't kill everything
        $data    = [];
        $errors  = [];
        $labels  = [
            'business'     => 'Business settings',
            'locations'    => 'Locations',
            'categories'   => 'Categories',
            'brands'       => 'Brands',
            'units'        => 'Units',
            'tax_rates'    => 'Tax rates',
            'contacts'     => 'Contacts',
            'products'     => 'Products',
            'stock'        => 'Stock levels',
            'transactions' => 'Transactions (30d)',
        ];

        foreach ($labels as $key => $label) {
            try {
                $method      = 'pull' . ucfirst(str_replace('_', '', ucwords($key, '_')));
                $data[$key]  = method_exists($this, $method)
                    ? $this->$method($businessId, $since)
                    : [];
            } catch (\Throwable $e) {
                $data[$key]  = [];
                $errors[$key] = $e->getMessage();
            }
        }

        $summary = array_map('count', $data);

        // Update token timestamps
        $token->last_pulled_at = $pulledAt;
        $token->last_seen_at   = $pulledAt;
        $token->save();

        // Log the pull
        try {
            SyncLog::create([
                'business_id'      => $businessId,
                'sync_token_id'    => $token->id,
                'direction'        => 'pull',
                'status'           => empty($errors) ? 'success' : 'partial',
                'summary'          => $summary,
                'errors'           => $errors ?: null,
                'records_sent'     => array_sum($summary),
                'records_received' => 0,
                'synced_at'        => $pulledAt,
            ]);
        } catch (\Throwable $e) { /* log table may not exist yet */ }

        return $this->syncResponse([
            'pulled_at'  => $pulledAt->toIso8601String(),
            'is_full'    => $since === null,
            'summary'    => $summary,
            'labels'     => $labels,
            'errors'     => $errors,
            'data'       => $data,
        ]);
    }

    // ── Push: Device → Server ────────────────────────────────────────────────

    /**
     * Accept changes made offline and merge them into the server database.
     * Transactions are appended; contacts/products are upserted by updated_at.
     */
    public function push(Request $request)
    {
        [$token, $errorResponse] = $this->resolveToken($request);
        if ($errorResponse) return $errorResponse;

        $businessId = $token->business_id;
        $pushedAt   = now();
        $errors     = [];
        $summary    = [];
        $conflicts  = 0;

        DB::beginTransaction();
        try {
            // ── 1. New Contacts ───────────────────────────────────────────
            $contacts = $request->input('contacts', []);
            $summary['contacts'] = 0;
            foreach ($contacts as $c) {
                try {
                    $this->upsertContact($businessId, $c, $conflicts);
                    $summary['contacts']++;
                } catch (\Exception $e) {
                    $errors[] = "Contact [{$c['name']}]: " . $e->getMessage();
                }
            }

            // ── 2. Transactions (Sales made offline) ──────────────────────
            $transactions = $request->input('transactions', []);
            $summary['transactions'] = 0;
            foreach ($transactions as $t) {
                try {
                    $this->insertOfflineTransaction($businessId, $t, $token);
                    $summary['transactions']++;
                } catch (\Exception $e) {
                    $errors[] = "Transaction [{$t['offline_id']}]: " . $e->getMessage();
                }
            }

            // ── 3. Stock Adjustments from offline ─────────────────────────
            $adjustments = $request->input('stock_adjustments', []);
            $summary['stock_adjustments'] = 0;
            foreach ($adjustments as $adj) {
                try {
                    $this->applyStockAdjustment($businessId, $adj);
                    $summary['stock_adjustments']++;
                } catch (\Exception $e) {
                    $errors[] = "StockAdj: " . $e->getMessage();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->syncResponse([
                'error'   => 'Push failed: ' . $e->getMessage(),
                'partial' => $summary,
            ], 500);
        }

        // Update token
        $token->last_pushed_at = $pushedAt;
        $token->last_seen_at   = $pushedAt;
        $token->save();

        $status = empty($errors) ? 'success' : (array_sum($summary) > 0 ? 'partial' : 'failed');

        SyncLog::create([
            'business_id'      => $businessId,
            'sync_token_id'    => $token->id,
            'direction'        => 'push',
            'status'           => $status,
            'summary'          => $summary,
            'errors'           => $errors ?: null,
            'records_sent'     => 0,
            'records_received' => array_sum($summary),
            'conflicts'        => $conflicts,
            'synced_at'        => $pushedAt,
        ]);

        return $this->syncResponse([
            'pushed_at' => $pushedAt->toIso8601String(),
            'status'    => $status,
            'summary'   => $summary,
            'conflicts' => $conflicts,
            'errors'    => $errors,
        ]);
    }

    // ── Status / Heartbeat ───────────────────────────────────────────────────

    public function status(Request $request)
    {
        [$token, $errorResponse] = $this->resolveToken($request);
        if ($errorResponse) return $errorResponse;

        $token->touch_seen();

        $last_log = SyncLog::where('sync_token_id', $token->id)
            ->orderBy('synced_at', 'desc')
            ->first();

        return $this->syncResponse([
            'online'           => true,
            'device_id'        => $token->id,
            'device_name'      => $token->device_name,
            'last_pulled_at'   => optional($token->last_pulled_at)->toIso8601String(),
            'last_pushed_at'   => optional($token->last_pushed_at)->toIso8601String(),
            'last_sync_status' => optional($last_log)->status,
        ]);
    }

    // ── Dashboard (Web) ──────────────────────────────────────────────────────

    public function dashboard(Request $request)
    {
        $businessId = session('business.id') ?? auth()->user()->business_id;

        // Guard against tables not yet migrated
        try {
            $tokens = SyncToken::where('business_id', $businessId)
                ->with('user')
                ->orderBy('last_seen_at', 'desc')
                ->get();
        } catch (\Throwable $e) {
            $tokens = collect();
        }

        try {
            $logs = SyncLog::where('business_id', $businessId)
                ->with('token')
                ->orderBy('synced_at', 'desc')
                ->limit(50)
                ->get();
        } catch (\Throwable $e) {
            $logs = collect();
        }

        try {
            $conflicts = DB::table('sync_conflicts')
                ->where('business_id', $businessId)
                ->where('resolution', 'pending')
                ->count();
        } catch (\Throwable $e) {
            $conflicts = 0;
        }

        return view('sync.dashboard', compact('tokens', 'logs', 'conflicts', 'businessId'));
    }

    public function revokeToken(Request $request, $id)
    {
        $businessId = session('business.id') ?? auth()->user()->business_id;
        $token = SyncToken::where('id', $id)->where('business_id', $businessId)->firstOrFail();
        $token->update(['is_active' => false]);

        return $this->syncResponse(['message' => 'Device token revoked.']);
    }

    // ── Private: Pull Helpers ────────────────────────────────────────────────

    private function pullBusiness(int $businessId, $since = null): array
    {
        $b = Business::find($businessId);
        return $b ? $b->only([
            'id', 'name', 'currency_id', 'time_zone', 'tax_label_1',
            'default_tax_id', 'sell_price_tax', 'fy_start_month',
        ]) : [];
    }

    private function pullLocations(int $businessId, $since): array
    {
        return BusinessLocation::where('business_id', $businessId)
            ->when($since, fn($q) => $q->where('updated_at', '>', $since))
            ->select(['id', 'name', 'landmark', 'mobile', 'email', 'updated_at'])
            ->get()->toArray();
    }

    private function pullCategories(int $businessId, $since): array
    {
        return \App\Category::where('business_id', $businessId)
            ->when($since, fn($q) => $q->where('updated_at', '>', $since))
            ->select(['id', 'name', 'parent_id', 'updated_at'])
            ->get()->toArray();
    }

    private function pullBrands(int $businessId, $since): array
    {
        return \App\Brands::where('business_id', $businessId)
            ->when($since, fn($q) => $q->where('updated_at', '>', $since))
            ->select(['id', 'name', 'updated_at'])
            ->get()->toArray();
    }

    private function pullUnits(int $businessId, $since): array
    {
        return \App\Unit::where('business_id', $businessId)
            ->when($since, fn($q) => $q->where('updated_at', '>', $since))
            ->select(['id', 'actual_name', 'short_name', 'updated_at'])
            ->get()->toArray();
    }

    private function pullTaxRates(int $businessId, $since): array
    {
        return \App\TaxRate::where('business_id', $businessId)
            ->when($since, fn($q) => $q->where('updated_at', '>', $since))
            ->select(['id', 'name', 'amount', 'is_tax_group', 'updated_at'])
            ->get()->toArray();
    }

    private function pullContacts(int $businessId, $since): array
    {
        return Contact::where('business_id', $businessId)
            ->whereIn('type', ['customer', 'both'])
            ->when($since, fn($q) => $q->where('updated_at', '>', $since))
            ->select([
                'id', 'type', 'first_name', 'last_name', 'name',
                'mobile', 'email', 'credit_limit', 'updated_at',
            ])
            ->get()->toArray();
    }

    private function pullProducts(int $businessId, $since): array
    {
        $products = Product::where('business_id', $businessId)
            ->where('status', 'active')
            ->when($since, fn($q) => $q->where('updated_at', '>', $since))
            ->with(['variations' => function ($q) {
                $q->select([
                    'id', 'product_id', 'name', 'sub_sku',
                    'default_sell_price', 'sell_price_inc_tax',
                ]);
            }])
            ->select([
                'id', 'name', 'sku', 'type', 'category_id', 'brand_id',
                'unit_id', 'tax', 'tax_type', 'enable_stock', 'updated_at',
            ])
            ->get();

        return $products->toArray();
    }

    private function pullStock(int $businessId, $since): array
    {
        return DB::table('variation_location_details as vld')
            ->join('business_locations as bl', 'bl.id', '=', 'vld.location_id')
            ->where('bl.business_id', $businessId)
            ->when($since, fn($q) => $q->where('vld.updated_at', '>', $since))
            ->select([
                'vld.variation_id',
                'vld.location_id',
                'vld.qty_available',
                'vld.updated_at',
            ])
            ->get()->toArray();
    }

    private function pullTransactions(int $businessId, $since): array
    {
        // Only pull recent sells for reference (last 30 days or since last pull)
        $cutoff = $since ?? now()->subDays(30);

        return Transaction::where('business_id', $businessId)
            ->where('type', 'sell')
            ->where('updated_at', '>', $cutoff)
            ->with(['sell_lines' => function ($q) {
                $q->select(['id', 'transaction_id', 'product_id', 'variation_id',
                            'quantity', 'unit_price', 'unit_price_inc_tax', 'line_discount_amount']);
            }])
            ->select([
                'id', 'invoice_no', 'contact_id', 'location_id', 'status',
                'payment_status', 'final_total', 'tax_amount', 'discount_amount',
                'transaction_date', 'updated_at',
            ])
            ->limit(500)
            ->get()->toArray();
    }

    // ── Private: Push Helpers ────────────────────────────────────────────────

    private function upsertContact(int $businessId, array $data, int &$conflicts): void
    {
        // If client sends an ID that already exists on server, check for conflict
        if (! empty($data['server_id'])) {
            $existing = Contact::where('id', $data['server_id'])
                ->where('business_id', $businessId)->first();

            if ($existing) {
                // Conflict: both sides changed
                if (isset($data['updated_at']) &&
                    $existing->updated_at > $data['updated_at']) {
                    // Server is newer — log conflict but don't overwrite
                    DB::table('sync_conflicts')->insert([
                        'business_id' => $businessId,
                        'model'       => 'Contact',
                        'record_id'   => $existing->id,
                        'server_data' => json_encode($existing->toArray()),
                        'client_data' => json_encode($data),
                        'resolution'  => 'server_wins',
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                    $conflicts++;
                    return;
                }
                $existing->update([
                    'first_name' => $data['first_name'] ?? $existing->first_name,
                    'last_name'  => $data['last_name']  ?? $existing->last_name,
                    'name'       => $data['name']        ?? $existing->name,
                    'mobile'     => $data['mobile']      ?? $existing->mobile,
                    'email'      => $data['email']       ?? $existing->email,
                ]);
                return;
            }
        }

        // New contact created offline
        Contact::create([
            'business_id' => $businessId,
            'type'        => $data['type'] ?? 'customer',
            'first_name'  => $data['first_name'] ?? '',
            'last_name'   => $data['last_name']  ?? '',
            'name'        => $data['name'] ?? trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
            'mobile'      => $data['mobile'] ?? null,
            'email'       => $data['email']  ?? null,
            'created_by'  => $data['created_by'] ?? null,
        ]);
    }

    private function insertOfflineTransaction(int $businessId, array $t, SyncToken $token): void
    {
        // Check idempotency: don't double-insert the same offline transaction
        $offlineRef = $t['offline_id'] ?? null;
        if ($offlineRef) {
            $exists = Transaction::where('business_id', $businessId)
                ->where('offline_ref', $offlineRef)
                ->exists();
            if ($exists) return; // already synced
        }

        $location = BusinessLocation::where('business_id', $businessId)
            ->first();

        $transaction = Transaction::create([
            'business_id'      => $businessId,
            'location_id'      => $t['location_id'] ?? optional($location)->id,
            'type'             => 'sell',
            'status'           => 'final',
            'payment_status'   => $t['payment_status'] ?? 'paid',
            'contact_id'       => $t['contact_id'] ?? null,
            'invoice_no'       => $t['invoice_no'] ?? $this->generateInvoiceNo($businessId),
            'final_total'      => $t['final_total'] ?? 0,
            'tax_amount'       => $t['tax_amount']  ?? 0,
            'discount_amount'  => $t['discount_amount'] ?? 0,
            'created_by'       => $t['created_by'] ?? optional($token->user)->id,
            'transaction_date' => $t['transaction_date'] ?? now(),
            'offline_ref'      => $offlineRef,
            'is_synced'        => 1,
        ]);

        // Insert sell lines
        foreach ($t['lines'] ?? [] as $line) {
            TransactionSellLine::create([
                'transaction_id'      => $transaction->id,
                'product_id'          => $line['product_id'],
                'variation_id'        => $line['variation_id'],
                'quantity'            => $line['quantity'],
                'unit_price'          => $line['unit_price'],
                'unit_price_inc_tax'  => $line['unit_price_inc_tax'] ?? $line['unit_price'],
                'line_discount_amount'=> $line['discount_amount'] ?? 0,
                'item_tax'            => $line['item_tax'] ?? 0,
                'tax_id'              => $line['tax_id'] ?? null,
            ]);
        }

        // Insert payments
        foreach ($t['payments'] ?? [] as $pay) {
            TransactionPayment::create([
                'transaction_id' => $transaction->id,
                'business_id'    => $businessId,
                'amount'         => $pay['amount'],
                'method'         => $pay['method'] ?? 'cash',
                'paid_on'        => $pay['paid_on'] ?? now(),
            ]);
        }

        // Deduct stock
        foreach ($t['lines'] ?? [] as $line) {
            $locationId = $t['location_id'] ?? optional($location)->id;
            if ($locationId) {
                DB::table('variation_location_details')
                    ->where('variation_id', $line['variation_id'])
                    ->where('location_id', $locationId)
                    ->decrement('qty_available', $line['quantity']);
            }
        }
    }

    private function applyStockAdjustment(int $businessId, array $adj): void
    {
        DB::table('variation_location_details')
            ->where('variation_id', $adj['variation_id'])
            ->where('location_id', $adj['location_id'])
            ->increment('qty_available', $adj['quantity_adjustment']);
    }

    private function generateInvoiceNo(int $businessId): string
    {
        $last = Transaction::where('business_id', $businessId)
            ->where('type', 'sell')
            ->max('id');
        return 'OFFLINE-' . $businessId . '-' . (($last ?? 0) + 1);
    }

    // ── Token Resolution ─────────────────────────────────────────────────────

    private function resolveToken(Request $request): array
    {
        $tokenStr = $request->header('X-Sync-Token')
            ?? $request->input('sync_token');

        // ── Session fallback: browser dashboard buttons (user already logged in) ──
        // When no X-Sync-Token is present but the user has an active Laravel session,
        // auto-create (or reuse) a browser token so Pull/Push work without pre-registration.
        if (! $tokenStr && auth()->check()) {
            $businessId = session('business.id') ?? auth()->user()->business_id;
            $token = SyncToken::firstOrCreate(
                [
                    'user_id'     => auth()->id(),
                    'business_id' => $businessId,
                    'device_type' => 'browser',
                    'device_name' => 'Dashboard Browser',
                ],
                [
                    'token'     => SyncToken::generate(),
                    'is_active' => true,
                ]
            );
            return [$token, null];
        }

        if (! $tokenStr) {
            return [null, $this->syncResponse(['error' => 'Missing X-Sync-Token header or sync_token param.'], 401)];
        }

        $token = SyncToken::where('token', $tokenStr)->where('is_active', true)->first();

        if (! $token) {
            return [null, $this->syncResponse(['error' => 'Invalid or revoked sync token. Re-register this device.'], 401)];
        }

        return [$token, null];
    }
}
