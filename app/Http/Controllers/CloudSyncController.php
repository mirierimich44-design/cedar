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
            ['http://localhost', 'http://127.0.0.1', 'http://reenson.test',
             'https://reenson.apextechsolutions.co.ke', 'http://reenson.apextechsolutions.co.ke'],
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

        // Allow the client to drive the delta window.
        // If the request body includes 'since', use that (null = full sync).
        // Otherwise fall back to the token's last_pulled_at (legacy behaviour).
        if ($request->has('since')) {
            $sinceRaw = $request->input('since');
            $since    = $sinceRaw ? \Carbon\Carbon::parse($sinceRaw) : null;
        } else {
            $since = $token->last_pulled_at;   // null = full sync
        }
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

    // ── Remote Push: collect local changes and send TO live server ───────────
    // Called by the Push button on Laragon. Gathers contacts/transactions created
    // since the last push and sends them to the live server's /sync/push endpoint.

    public function pushRemote(Request $request)
    {
        $businessId = session('business.id') ?? auth()->user()->business_id;
        $settings   = self::readSettings($businessId);

        $remoteUrl = rtrim($settings['remote_url'] ?? '', '/');
        $token     = $settings['sync_token'] ?? '';

        if (! $remoteUrl || ! $token) {
            return response()->json(['error' => 'Remote URL and token required. Configure in ⚙ Settings.'], 422);
        }

        // ── Determine cutoff: only push records created AFTER last pull ───────
        // This prevents re-pushing contacts that originally came FROM the live server.
        $pushFile = storage_path("app/sync_last_pushed_{$businessId}.json");
        $pullFile = storage_path("app/sync_last_pulled_{$businessId}.json");

        $lastPushed = file_exists($pushFile)
            ? (json_decode(file_get_contents($pushFile), true)['pushed_at'] ?? null) : null;
        $lastPulled = file_exists($pullFile)
            ? (json_decode(file_get_contents($pullFile), true)['pulled_at'] ?? null) : null;

        // Use the most recent of lastPushed or lastPulled as our cutoff.
        // This means: only send records created/updated AFTER we last synced from live.
        $cutoffStr = $lastPushed ?? $lastPulled ?? null;
        $since     = $cutoffStr ? \Carbon\Carbon::parse($cutoffStr) : null;

        // ── Collect locally created contacts (after cutoff) ───────────────────
        $contacts = DB::table('contacts')
            ->where('business_id', $businessId)
            ->when($since, fn($q) => $q->where('created_at', '>', $since))
            ->select(['id','type','first_name','last_name','name','mobile','email',
                      'credit_limit','created_by','created_at','updated_at'])
            ->get()
            ->map(fn($c) => (array) $c)   // no server_id: tell live server to create fresh
            ->toArray();

        // ── Collect unsynced sells + purchases with lines & payments ─────────
        $hasSyncedCol = \Illuminate\Support\Facades\Schema::hasColumn('transactions', 'is_synced');
        $txRows = DB::table('transactions')
            ->where('business_id', $businessId)
            ->whereIn('type', ['sell', 'purchase'])
            ->where('status', 'final')
            ->when($since,        fn($q) => $q->where('created_at', '>', $since))
            ->when($hasSyncedCol, fn($q) => $q->where(fn($q2) =>
                $q2->whereNull('is_synced')->orWhere('is_synced', 0)
            ))
            ->limit(200)
            ->get();

        $transactions = $txRows->map(function($t) {
            $tx = (array) $t;

            // Sell lines
            $tx['sell_lines'] = DB::table('transaction_sell_lines')
                ->where('transaction_id', $t->id)
                ->get()->map(fn($l) => (array) $l)->toArray();

            // Purchase lines
            $tx['purchase_lines'] = DB::table('purchase_lines')
                ->where('transaction_id', $t->id)
                ->get()->map(fn($l) => (array) $l)->toArray();

            // Payments (method, amount, note, transaction_no, etc.)
            $tx['payments'] = DB::table('transaction_payments')
                ->where('transaction_id', $t->id)
                ->get()->map(fn($p) => (array) $p)->toArray();

            return $tx;
        })->toArray();

        $nowStr = now()->toIso8601String();

        if (empty($contacts) && empty($transactions)) {
            return response()->json([
                'pushed_at' => $nowStr,
                'status'    => 'success',
                'summary'   => ['contacts' => 0, 'transactions' => 0],
                'labels'    => ['contacts' => 'Contacts', 'transactions' => 'Transactions'],
                'errors'    => [],
                'message'   => 'Nothing new to push.' . ($cutoffStr ? ' (since ' . \Carbon\Carbon::parse($cutoffStr)->format('d M H:i') . ')' : ''),
            ]);
        }

        // ── Send to live server ───────────────────────────────────────────────
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(60)
                ->withHeaders(['X-Sync-Token' => $token, 'Accept' => 'application/json'])
                ->post("{$remoteUrl}/sync/push", [
                    'sync_token'   => $token,
                    'business_id'  => $businessId,
                    'contacts'     => $contacts,
                    'transactions' => $transactions,
                ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not reach live server: ' . $e->getMessage()], 503);
        }

        if ($response->failed()) {
            return response()->json([
                'error' => 'Live server returned HTTP ' . $response->status() . ': ' . substr($response->body(), 0, 400),
            ], 502);
        }

        $body = $response->json();
        if (isset($body['error'])) {
            return response()->json(['error' => $body['error']], 502);
        }

        $remoteErrors = $body['errors'] ?? [];

        // Only advance the push timestamp if everything succeeded.
        // If there were errors, keep the old cutoff so failed records are retried next time.
        if (empty($remoteErrors)) {
            file_put_contents($pushFile, json_encode(['pushed_at' => $nowStr]));
        }

        return response()->json([
            'pushed_at'   => $body['pushed_at'] ?? $nowStr,
            'status'      => $body['status'] ?? (empty($remoteErrors) ? 'success' : 'partial'),
            'summary'     => $body['summary'] ?? ['contacts' => count($contacts), 'transactions' => count($transactions)],
            'labels'      => ['contacts' => 'Contacts', 'transactions' => 'Transactions', 'stock_adjustments' => 'Stock adjustments'],
            'sent_counts' => ['contacts' => count($contacts), 'transactions' => count($transactions)],
            'errors'      => $remoteErrors,
        ]);
    }

    // ── Remote Pull: fetch FROM live server and save locally (for Laragon) ─────
    // Called by the Pull button when a remote_url is configured in settings.
    // Does a server-to-server HTTP call so no CORS issues, no terminal needed.

    public function pullRemote(Request $request)
    {
        $businessId = session('business.id') ?? auth()->user()->business_id;
        $settings   = self::readSettings($businessId);

        $remoteUrl = rtrim($settings['remote_url'] ?? '', '/');
        $token     = $settings['sync_token'] ?? '';

        if (! $remoteUrl) {
            return response()->json(['error' => 'No remote URL configured. Use ⚙ Settings to set the live server URL.'], 422);
        }
        if (! $token) {
            return response()->json(['error' => 'No sync token saved. Register a Desktop device on the live server and paste the token in ⚙ Settings.'], 422);
        }

        // Read the timestamp of the last successful pull from local storage.
        // Passing this as 'since' tells the live server exactly what window to send.
        // If no local pull has ever happened ($localSince = null), the live server
        // performs a full sync regardless of what its token timestamp says.
        $pullFile   = storage_path("app/sync_last_pulled_{$businessId}.json");
        $localSince = null;
        if (file_exists($pullFile)) {
            $saved = json_decode(file_get_contents($pullFile), true);
            $localSince = $saved['pulled_at'] ?? null;
        }

        // Call the live server's pull endpoint (server-to-server, no CORS)
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(120)
                ->withHeaders(['X-Sync-Token' => $token, 'Accept' => 'application/json'])
                ->post("{$remoteUrl}/sync/pull", [
                    'sync_token'  => $token,
                    'business_id' => $businessId,
                    'since'       => $localSince,   // null = full sync, timestamp = delta
                ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not reach live server: ' . $e->getMessage()], 503);
        }

        if ($response->failed()) {
            return response()->json(['error' => 'Live server returned ' . $response->status() . ': ' . $response->body()], 502);
        }

        $body = $response->json();

        if (isset($body['error'])) {
            return response()->json(['error' => $body['error']], 502);
        }

        // Save pulled data into local database
        $data   = $body['data']    ?? [];
        $errors = [];

        // Get actual columns for each table so we never pass unknown columns
        $txCols      = DB::getSchemaBuilder()->getColumnListing('transactions');
        $sellLineCols= DB::getSchemaBuilder()->getColumnListing('transaction_sell_lines');
        $purLineCols = DB::getSchemaBuilder()->getColumnListing('purchase_lines');
        $paymentCols = DB::getSchemaBuilder()->getColumnListing('transaction_payments');
        $stockCols   = DB::getSchemaBuilder()->getColumnListing('variation_location_details');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            // ── Lookup tables ─────────────────────────────────────────────────
            foreach ($data['categories'] ?? [] as $r) { if (!empty($r['id'])) DB::table('categories')->updateOrInsert(['id'=>$r['id']], $r); }
            foreach ($data['brands']     ?? [] as $r) { if (!empty($r['id'])) DB::table('brands')->updateOrInsert(['id'=>$r['id']], $r); }
            foreach ($data['units']      ?? [] as $r) { if (!empty($r['id'])) DB::table('units')->updateOrInsert(['id'=>$r['id']], $r); }
            foreach ($data['tax_rates']  ?? [] as $r) { if (!empty($r['id'])) DB::table('tax_rates')->updateOrInsert(['id'=>$r['id']], $r); }
            foreach ($data['contacts']   ?? [] as $r) { if (!empty($r['id'])) DB::table('contacts')->updateOrInsert(['id'=>$r['id']], $r); }

            // ── Products + variations ─────────────────────────────────────────
            $productCols   = DB::getSchemaBuilder()->getColumnListing('products');
            $variationCols = DB::getSchemaBuilder()->getColumnListing('variations');

            foreach ($data['products'] ?? [] as $p) {
                if (empty($p['id'])) continue;
                $variations = $p['variations'] ?? [];
                unset($p['variations'], $p['sell_lines'], $p['purchase_lines'], $p['payments']);
                $safeP = array_intersect_key((array)$p, array_flip($productCols));
                DB::table('products')->updateOrInsert(['id'=>$p['id']], $safeP);
                foreach ($variations as $v) {
                    if (empty($v['id'])) continue;
                    $safeV = array_intersect_key((array)$v, array_flip($variationCols));
                    DB::table('variations')->updateOrInsert(['id'=>$v['id']], $safeV);
                }
            }

            // ── Stock levels ──────────────────────────────────────────────────
            // The live server now sends product_id + product_variation_id so we
            // can INSERT new rows on a fresh Laragon (not just UPDATE existing ones).
            foreach ($data['stock'] ?? [] as $r) {
                if (empty($r['variation_id']) || empty($r['location_id'])) continue;
                $safe = array_intersect_key((array)$r, array_flip($stockCols));
                // Remove auto-increment 'id' so MySQL assigns its own on insert
                unset($safe['id']);
                DB::table('variation_location_details')->updateOrInsert(
                    ['variation_id' => $r['variation_id'], 'location_id' => $r['location_id']],
                    $safe
                );
            }

            // ── Transactions: sells + purchases with lines & payments ─────────
            foreach ($data['transactions'] ?? [] as $t) {
                if (empty($t['id'])) continue;
                try {
                    $sellLines    = $t['sell_lines']     ?? [];
                    $purchLines   = $t['purchase_lines'] ?? [];
                    $payments     = $t['payments']       ?? [];

                    // Save transaction row (only known columns)
                    $txRow = array_intersect_key((array)$t, array_flip($txCols));
                    unset($txRow['sell_lines'], $txRow['purchase_lines'], $txRow['payments']);
                    DB::table('transactions')->updateOrInsert(['id'=>$t['id']], $txRow);

                    // Save sell lines
                    foreach ($sellLines as $line) {
                        if (empty($line['id'])) continue;
                        $safe = array_intersect_key((array)$line, array_flip($sellLineCols));
                        DB::table('transaction_sell_lines')->updateOrInsert(['id'=>$line['id']], $safe);
                    }

                    // Save purchase lines
                    foreach ($purchLines as $line) {
                        if (empty($line['id'])) continue;
                        $safe = array_intersect_key((array)$line, array_flip($purLineCols));
                        DB::table('purchase_lines')->updateOrInsert(['id'=>$line['id']], $safe);
                    }

                    // Save payments
                    foreach ($payments as $pay) {
                        if (empty($pay['id'])) continue;
                        $safe = array_intersect_key((array)$pay, array_flip($paymentCols));
                        DB::table('transaction_payments')->updateOrInsert(['id'=>$pay['id']], $safe);
                    }
                } catch (\Throwable $e) {
                    $errors[] = "Transaction [{$t['id']}]: " . $e->getMessage();
                }
            }

        } catch (\Throwable $e) {
            $errors[] = $e->getMessage();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        // Save pull timestamp — pushRemote uses this as cutoff so only locally-created
        // records (after this timestamp) are ever pushed back to the live server.
        $pullFile = storage_path("app/sync_last_pulled_{$businessId}.json");
        file_put_contents($pullFile, json_encode(['pulled_at' => $body['pulled_at'] ?? now()->toIso8601String()]));

        // Return same shape as normal pull so the UI renders the table
        return response()->json(array_merge($body, [
            'saved_locally' => true,
            'local_errors'  => $errors,
        ]));
    }

    // ── Sync Settings (stored in storage/app/sync_config_{id}.json) ──────────

    public function getSettings(Request $request)
    {
        $businessId = session('business.id') ?? auth()->user()->business_id;
        return response()->json($this->readSettings($businessId));
    }

    public function saveSettings(Request $request)
    {
        $businessId = session('business.id') ?? auth()->user()->business_id;
        $settings = [
            'remote_url'  => rtrim($request->input('remote_url', ''), '/'),
            'sync_token'  => $request->input('sync_token', ''),
            'business_id' => (int) $request->input('business_id', $businessId),
            'saved_at'    => now()->toIso8601String(),
        ];
        $path = storage_path("app/sync_config_{$businessId}.json");
        file_put_contents($path, json_encode($settings, JSON_PRETTY_PRINT));
        return response()->json(['ok' => true, 'settings' => $settings]);
    }

    public static function readSettings(int $businessId): array
    {
        $path = storage_path("app/sync_config_{$businessId}.json");
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            if (is_array($data)) return $data;
        }
        // Fall back to .env values
        return [
            'remote_url'  => env('SYNC_REMOTE_URL', ''),
            'sync_token'  => env('SYNC_TOKEN', ''),
            'business_id' => (int) env('SYNC_BUSINESS_ID', $businessId),
            'saved_at'    => null,
        ];
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
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('products', 'status'), fn($q) => $q->where('status', 'active'))
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
                'vld.id',
                'vld.product_id',
                'vld.product_variation_id',
                'vld.variation_id',
                'vld.location_id',
                'vld.qty_available',
                'vld.updated_at',
            ])
            ->get()->toArray();
    }

    private function pullTransactions(int $businessId, $since): array
    {
        $cutoff = $since ?? now()->subDays(30);

        $rows = DB::table('transactions')
            ->where('business_id', $businessId)
            ->whereIn('type', ['sell', 'purchase'])
            ->where('updated_at', '>', $cutoff)
            ->limit(500)
            ->get();

        return $rows->map(function($t) {
            $tx = (array) $t;

            $tx['sell_lines'] = DB::table('transaction_sell_lines')
                ->where('transaction_id', $t->id)
                ->get()->map(fn($l) => (array)$l)->toArray();

            $tx['purchase_lines'] = DB::table('purchase_lines')
                ->where('transaction_id', $t->id)
                ->get()->map(fn($l) => (array)$l)->toArray();

            $tx['payments'] = DB::table('transaction_payments')
                ->where('transaction_id', $t->id)
                ->get()->map(fn($p) => (array)$p)->toArray();

            return $tx;
        })->toArray();
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
        // created_by: use value from device, fall back to token owner, then first admin
        $createdBy = $data['created_by']
            ?? DB::table('users')->where('business_id', $businessId)->value('id')
            ?? 1;

        Contact::create([
            'business_id' => $businessId,
            'type'        => $data['type'] ?? 'customer',
            'first_name'  => $data['first_name'] ?? '',
            'last_name'   => $data['last_name']  ?? '',
            'name'        => $data['name'] ?? trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
            'mobile'      => $data['mobile'] ?? null,
            'email'       => $data['email']  ?? null,
            'created_by'  => $createdBy,
        ]);
    }

    private function insertOfflineTransaction(int $businessId, array $t, SyncToken $token): void
    {
        $type = $t['type'] ?? 'sell';

        // Idempotency: skip if this invoice already exists on the server
        $invoiceNo = $t['invoice_no'] ?? null;
        if ($invoiceNo) {
            $exists = DB::table('transactions')
                ->where('business_id', $businessId)
                ->where('invoice_no', $invoiceNo)
                ->where('type', $type)
                ->exists();
            if ($exists) return;
        }

        $location   = BusinessLocation::where('business_id', $businessId)->first();
        $createdBy  = $t['created_by'] ?? optional($token->user)->id
            ?? DB::table('users')->where('business_id', $businessId)->value('id') ?? 1;

        // Build transaction row — pass through ALL fields from the device
        $txData = array_filter([
            'business_id'      => $businessId,
            'location_id'      => $t['location_id']      ?? optional($location)->id,
            'type'             => $type,
            'status'           => $t['status']           ?? 'final',
            'payment_status'   => $t['payment_status']   ?? 'paid',
            'contact_id'       => $t['contact_id']       ?? null,
            'invoice_no'       => $invoiceNo              ?? $this->generateInvoiceNo($businessId),
            'ref_no'           => $t['ref_no']           ?? null,
            'final_total'      => $t['final_total']      ?? 0,
            'total_before_tax' => $t['total_before_tax'] ?? ($t['final_total'] ?? 0),
            'tax_amount'       => $t['tax_amount']       ?? 0,
            'discount_amount'  => $t['discount_amount']  ?? 0,
            'discount_type'    => $t['discount_type']    ?? 'fixed',
            'shipping_details' => $t['shipping_details'] ?? null,
            'shipping_charges' => $t['shipping_charges'] ?? 0,
            'additional_notes' => $t['additional_notes'] ?? null,
            'staff_note'       => $t['staff_note']       ?? null,
            'created_by'       => $createdBy,
            'transaction_date' => $t['transaction_date'] ?? now(),
            'is_synced'        => 1,
        ], fn($v) => $v !== null);

        $transaction = Transaction::create($txData);

        // ── Sell lines ────────────────────────────────────────────────────────
        foreach ($t['sell_lines'] ?? $t['lines'] ?? [] as $line) {
            TransactionSellLine::create([
                'transaction_id'       => $transaction->id,
                'product_id'           => $line['product_id'],
                'variation_id'         => $line['variation_id'],
                'quantity'             => $line['quantity'],
                'unit_price'           => $line['unit_price']           ?? 0,
                'unit_price_inc_tax'   => $line['unit_price_inc_tax']   ?? $line['unit_price'] ?? 0,
                'unit_price_before_discount' => $line['unit_price_before_discount'] ?? $line['unit_price'] ?? 0,
                'line_discount_amount' => $line['line_discount_amount'] ?? 0,
                'line_discount_type'   => $line['line_discount_type']   ?? 'fixed',
                'item_tax'             => $line['item_tax']             ?? 0,
                'tax_id'               => $line['tax_id']               ?? null,
                'sell_line_note'       => $line['sell_line_note']       ?? null,
                'sub_unit_id'          => $line['sub_unit_id']          ?? null,
            ]);
        }

        // ── Purchase lines ────────────────────────────────────────────────────
        foreach ($t['purchase_lines'] ?? [] as $line) {
            DB::table('purchase_lines')->insert([
                'transaction_id'       => $transaction->id,
                'product_id'           => $line['product_id'],
                'variation_id'         => $line['variation_id'],
                'quantity'             => $line['quantity'],
                'purchase_price'       => $line['purchase_price']       ?? 0,
                'purchase_price_inc_tax' => $line['purchase_price_inc_tax'] ?? $line['purchase_price'] ?? 0,
                'pp_without_discount'  => $line['pp_without_discount']  ?? $line['purchase_price'] ?? 0,
                'item_tax'             => $line['item_tax']             ?? 0,
                'tax_id'               => $line['tax_id']               ?? null,
                'quantity_adjusted'    => $line['quantity_adjusted']    ?? 0,
                'quantity_returned'    => $line['quantity_returned']    ?? 0,
                'exp_date'             => $line['exp_date']             ?? null,
                'lot_number'           => $line['lot_number']           ?? null,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }

        // ── Payments (full data including method, transaction_no, note etc.) ─
        foreach ($t['payments'] ?? [] as $pay) {
            TransactionPayment::create([
                'transaction_id'    => $transaction->id,
                'business_id'       => $businessId,
                'amount'            => $pay['amount'],
                'method'            => $pay['method']           ?? 'cash',
                'paid_on'           => $pay['paid_on']          ?? now(),
                'transaction_no'    => $pay['transaction_no']   ?? null,
                'payment_ref_no'    => $pay['payment_ref_no']   ?? null,
                'note'              => $pay['note']             ?? null,
                'card_number'       => $pay['card_number']      ?? null,
                'card_holder_name'  => $pay['card_holder_name'] ?? null,
                'cheque_number'     => $pay['cheque_number']    ?? null,
                'bank_account_number' => $pay['bank_account_number'] ?? null,
                'is_advance'        => $pay['is_advance']       ?? 0,
                'created_by'        => $createdBy,
                'is_synced'         => 1,
            ]);
        }

        // ── Update stock levels ───────────────────────────────────────────────
        $locationId = $t['location_id'] ?? optional($location)->id;
        if ($locationId) {
            foreach ($t['sell_lines'] ?? $t['lines'] ?? [] as $line) {
                DB::table('variation_location_details')
                    ->where('variation_id', $line['variation_id'])
                    ->where('location_id', $locationId)
                    ->decrement('qty_available', $line['quantity']);
            }
            foreach ($t['purchase_lines'] ?? [] as $line) {
                DB::table('variation_location_details')
                    ->where('variation_id', $line['variation_id'])
                    ->where('location_id', $locationId)
                    ->increment('qty_available', $line['quantity']);
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
