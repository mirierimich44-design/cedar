<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobilePosController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return response()->json(['message' => 'Invalid username or password.'], 401);
        }

        $user = Auth::user();

        if (!$user->business || !$user->business->is_active) {
            Auth::logout();
            return response()->json(['message' => 'Business account is inactive.'], 403);
        }

        if ($user->status !== 'active') {
            Auth::logout();
            return response()->json(['message' => 'Your account is inactive.'], 403);
        }

        if (!$user->allow_login) {
            Auth::logout();
            return response()->json(['message' => 'Login not allowed for this account.'], 403);
        }

        $user->tokens()->where('name', 'mobile-pos')->delete();
        $token = $user->createToken('mobile-pos')->accessToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'       => $user->id,
                'name'     => trim($user->first_name . ' ' . $user->last_name),
                'username' => $user->username,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Logged out.']);
    }

    public function posDetails(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;

        $register = \App\CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        $default_location = null;

        if ($register && $register->location_id) {
            $loc = \App\BusinessLocation::find($register->location_id);
            if ($loc) {
                $default_location = ['id' => $loc->id, 'name' => $loc->name];
            }
        }

        if (!$default_location) {
            $first = \App\BusinessLocation::where('business_id', $business_id)->first();
            if ($first) {
                $default_location = ['id' => $first->id, 'name' => $first->name];
            }
        }

        return response()->json([
            'user'             => [
                'id'       => $user->id,
                'name'     => trim($user->first_name . ' ' . $user->last_name),
                'username' => $user->username,
            ],
            'default_location' => $default_location,
        ]);
    }

    public function products(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;
        $location_id = $request->get('location_id');
        $term        = trim($request->get('term', ''));

        $query = DB::table('products')
            ->join('variations', 'products.id', '=', 'variations.product_id')
            ->leftJoin('variation_location_details as vld', function ($join) use ($location_id) {
                $join->on('variations.id', '=', 'vld.variation_id');
                if ($location_id) {
                    $join->where('vld.location_id', $location_id);
                }
            })
            ->where('products.business_id', $business_id)
            ->where('products.not_for_selling', 0)
            ->where('products.status', 'active')
            ->select([
                'products.id as product_id',
                'variations.id as variation_id',
                'products.name',
                'variations.name as variation',
                'variations.sub_sku',
                'products.enable_stock',
                DB::raw('COALESCE(vld.qty_available, 0) as qty_available'),
                DB::raw('variations.default_sell_price as selling_price'),
            ]);

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('products.name', 'like', "%{$term}%")
                  ->orWhere('variations.sub_sku', 'like', "%{$term}%");
            });
        }

        // Only exclude out-of-stock for products that track stock
        $query->where(function ($q) {
            $q->where('products.enable_stock', 0)
              ->orWhereRaw('COALESCE(vld.qty_available, 0) > 0');
        });

        $products = $query->orderBy('products.name')->limit(150)->get();

        return response()->json(['products' => $products]);
    }

    public function createSale(Request $request)
    {
        $user = $request->user();
        Auth::guard('web')->setUser($user);
        $request->session()->put('user.business_id', $user->business_id);
        $request->session()->put('user.id', $user->id);

        $controller = app(\App\Http\Controllers\SellPosController::class);
        return $controller->store($request);
    }

    public function paymentTypes(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;

        $types = \App\BusinessPaymentMethod::where('business_id', $business_id)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'account_id'])
            ->map(fn($m) => [
                'id'    => $m->name,
                'label' => ucfirst(str_replace('_', ' ', $m->name)),
            ]);

        if ($types->isEmpty()) {
            $types = collect([
                ['id' => 'cash',  'label' => 'Cash'],
                ['id' => 'card',  'label' => 'Card'],
                ['id' => 'mpesa', 'label' => 'M-Pesa'],
            ]);
        }

        return response()->json(['payment_types' => $types]);
    }

    public function expenseCategories(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;

        $categories = \App\ExpenseCategory::where('business_id', $business_id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['categories' => $categories]);
    }

    public function createExpense(Request $request)
    {
        $user = $request->user();
        Auth::guard('web')->setUser($user);
        $request->session()->put('user.business_id', $user->business_id);
        $request->session()->put('user.id', $user->id);

        $controller = app(\App\Http\Controllers\ExpenseController::class);
        return $controller->store($request);
    }

    /**
     * GET /api/mobile/till-summary
     * Today's sales with totals per payment method + optional register info.
     */
    public function tillSummary(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;
        $location_id = $request->get('location_id');
        $today       = now()->toDateString();

        // Sales count & subtotal for today
        $salesQuery = DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereDate('transaction_date', $today);

        if ($location_id) {
            $salesQuery->where('location_id', $location_id);
        }

        $salesInfo = $salesQuery->selectRaw('COUNT(*) as count, COALESCE(SUM(final_total), 0) as total')
            ->first();

        // Breakdown by payment method
        $payBreakdown = DB::table('transaction_payments as tp')
            ->join('transactions as t', 't.id', '=', 'tp.transaction_id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'sell')
            ->where('t.status', 'final')
            ->whereDate('t.transaction_date', $today)
            ->when($location_id, fn($q) => $q->where('t.location_id', $location_id))
            ->selectRaw('tp.method, COALESCE(SUM(tp.amount), 0) as total')
            ->groupBy('tp.method')
            ->orderBy('total', 'desc')
            ->get();

        // Check if a cash register is open for this user
        $register = \App\CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->when($location_id, fn($q) => $q->where('location_id', $location_id))
            ->first();

        return response()->json([
            'date'          => $today,
            'sales_count'   => (int) $salesInfo->count,
            'sales_total'   => (float) $salesInfo->total,
            'payment_breakdown' => $payBreakdown,
            'register_open' => (bool) $register,
            'register_id'   => $register?->id,
        ]);
    }

    /**
     * POST /api/mobile/close-till
     * Closes the open cash register for this user.
     */
    public function closeTill(Request $request)
    {
        $user = $request->user();

        $register = \App\CashRegister::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if (!$register) {
            return response()->json(['message' => 'No open till found.'], 404);
        }

        $register->status         = 'close';
        $register->closing_amount = $request->get('closing_amount', 0);
        $register->closed_at      = now();
        $register->save();

        return response()->json(['message' => 'Till closed successfully.']);
    }
}
