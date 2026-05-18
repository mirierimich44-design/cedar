<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Transaction;

class SmartAlertsController extends Controller
{
    public function index(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $alerts = [];
        $total_count = 0;

        // 1. Low stock alert
        try {
            $low_stock_count = \DB::table('products')
                ->join('product_variations', 'products.id', '=', 'product_variations.product_id')
                ->join('variation_location_details as vld', 'product_variations.id', '=', 'vld.product_variation_id')
                ->where('products.business_id', $business_id)
                ->where('products.enable_stock', 1)
                ->whereNotNull('products.alert_quantity')
                ->whereRaw('vld.qty_available <= products.alert_quantity')
                ->count();

            if ($low_stock_count > 0) {
                $alerts[] = [
                    'type'    => 'low_stock',
                    'title'   => 'Low Stock Alert',
                    'message' => $low_stock_count . ' product(s) below reorder level',
                    'count'   => $low_stock_count,
                    'url'     => route('reports.stock_alert'),
                ];
                $total_count += $low_stock_count;
            }
        } catch (\Exception $e) {}

        // 2. Pending M-Pesa C2B payments
        try {
            if (class_exists(\App\MpesaC2bPayment::class)) {
                $pending_mpesa = \App\MpesaC2bPayment::where('business_id', $business_id)
                    ->whereIn('status', ['received', 'unmatched'])
                    ->count();
                if ($pending_mpesa > 0) {
                    $alerts[] = [
                        'type'    => 'pending_mpesa',
                        'title'   => 'Unmatched M-Pesa Payments',
                        'message' => $pending_mpesa . ' payment(s) waiting to be matched',
                        'count'   => $pending_mpesa,
                        'url'     => route('mpesa.c2b_payments'),
                    ];
                    $total_count += $pending_mpesa;
                }
            }
        } catch (\Exception $e) {}

        // 3. Overdue invoices (due date passed, not fully paid)
        try {
            $overdue_count = Transaction::where('business_id', $business_id)
                ->where('type', 'sell')
                ->where('payment_status', '!=', 'paid')
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->toDateString())
                ->count();
            if ($overdue_count > 0) {
                $alerts[] = [
                    'type'    => 'overdue_payments',
                    'title'   => 'Overdue Invoices',
                    'message' => $overdue_count . ' invoice(s) past due date',
                    'count'   => $overdue_count,
                    'url'     => action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer']),
                ];
                $total_count += $overdue_count;
            }
        } catch (\Exception $e) {}

        // 4. Gym subscriptions expiring in 3 days
        try {
            if (\Module::has('Gym') && class_exists(\Modules\Gym\Entities\GymMembership::class)) {
                $expiring_soon = \Modules\Gym\Entities\GymMembership::where('business_id', $business_id)
                    ->where('status', 'active')
                    ->whereBetween('end_date', [now()->toDateString(), now()->addDays(3)->toDateString()])
                    ->count();
                if ($expiring_soon > 0) {
                    $alerts[] = [
                        'type'    => 'gym_expiry',
                        'title'   => 'Gym Subscriptions Expiring',
                        'message' => $expiring_soon . ' membership(s) expiring within 3 days',
                        'count'   => $expiring_soon,
                        'url'     => route('gym.subscriptions.index'),
                    ];
                    $total_count += $expiring_soon;
                }
            }
        } catch (\Exception $e) {}

        return response()->json([
            'alerts' => $alerts,
            'total_alert_count' => $total_count,
        ]);
    }
}
