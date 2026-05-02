<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobilePosController extends Controller
{
    /**
     * POST /api/mobile/login
     * Authenticates with username + password, returns a Passport token.
     */
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

        // Revoke any previous mobile tokens to keep it clean
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

    /**
     * POST /api/mobile/logout
     * Revokes the current token.
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Logged out.']);
    }

    /**
     * GET /api/mobile/pos-details
     * Returns the user's default business location.
     */
    public function posDetails(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;

        // Try to find the location from an open cash register first
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
            'user' => [
                'id'       => $user->id,
                'name'     => trim($user->first_name . ' ' . $user->last_name),
                'username' => $user->username,
            ],
            'default_location' => $default_location,
        ]);
    }

    /**
     * GET /api/mobile/products?location_id=&term=
     * Returns in-stock products for the given location.
     */
    public function products(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;
        $location_id = $request->get('location_id');
        $term        = $request->get('term', '');

        $query = \DB::table('products')
            ->join('variations', 'products.id', '=', 'variations.product_id')
            ->leftJoin('variation_location_details as vld', function ($join) use ($location_id) {
                $join->on('variations.id', '=', 'vld.variation_id')
                     ->where('vld.location_id', $location_id);
            })
            ->where('products.business_id', $business_id)
            ->where('products.not_for_selling', 0)
            ->where('products.status', 'active')
            ->where('variations.is_dummy', 0)
            ->select([
                'products.id as product_id',
                'variations.id as variation_id',
                'products.name',
                'variations.name as variation',
                'variations.sub_sku',
                'products.enable_stock',
                \DB::raw('COALESCE(vld.qty_available, 0) as qty_available'),
                \DB::raw('variations.default_sell_price as selling_price'),
            ]);

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('products.name', 'like', "%{$term}%")
                  ->orWhere('variations.sub_sku', 'like', "%{$term}%");
            });
        }

        // Exclude out-of-stock items that track stock
        $query->where(function ($q) {
            $q->where('products.enable_stock', 0)
              ->orWhere(\DB::raw('COALESCE(vld.qty_available, 0)'), '>', 0);
        });

        $products = $query->orderBy('products.name')->limit(100)->get();

        return response()->json(['products' => $products]);
    }

    /**
     * POST /api/mobile/sale
     * Creates a POS sale by delegating to the existing SellPosController::store().
     */
    public function createSale(Request $request)
    {
        $user = $request->user();

        Auth::guard('web')->setUser($user);
        $request->session()->put('user.business_id', $user->business_id);
        $request->session()->put('user.id', $user->id);

        $controller = app(\App\Http\Controllers\SellPosController::class);
        return $controller->store($request);
    }

    /**
     * GET /api/mobile/payment-types
     * Returns enabled payment methods for the business.
     */
    public function paymentTypes(Request $request)
    {
        $user        = $request->user();
        $business_id = $user->business_id;

        $types = \App\BusinessPaymentMethod::where('business_id', $business_id)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'account_id'])
            ->map(fn($m) => ['id' => $m->name, 'label' => ucfirst(str_replace('_', ' ', $m->name))]);

        // Fallback to defaults if none configured
        if ($types->isEmpty()) {
            $types = collect([
                ['id' => 'cash',  'label' => 'Cash'],
                ['id' => 'card',  'label' => 'Card'],
                ['id' => 'mpesa', 'label' => 'M-Pesa'],
            ]);
        }

        return response()->json(['payment_types' => $types]);
    }

    /**
     * GET /api/mobile/expense-categories
     * Returns expense categories for the business.
     */
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

    /**
     * POST /api/mobile/expense
     * Creates an expense by delegating to the existing ExpenseController::store().
     */
    public function createExpense(Request $request)
    {
        $user = $request->user();

        Auth::guard('web')->setUser($user);
        $request->session()->put('user.business_id', $user->business_id);
        $request->session()->put('user.id', $user->id);

        $controller = app(\App\Http\Controllers\ExpenseController::class);
        return $controller->store($request);
    }
}
