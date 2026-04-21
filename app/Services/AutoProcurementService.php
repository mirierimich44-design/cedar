<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoProcurementService
{
    /**
     * Scan for low-stock products and return a reorder report.
     * Does NOT create transactions — returns structured data for the BI dashboard.
     */
    public function run(): array
    {
        $business_id = request()->session()->get('user.business_id');

        // purchase_price lives on variations.default_purchase_price, not on products
        $low_stock = DB::table('products')
            ->join('variation_location_details as vld', 'vld.product_id', '=', 'products.id')
            ->join('variations as v', 'v.product_id', '=', 'products.id')
            ->where('products.business_id', $business_id)
            ->where('products.alert_quantity', '>', 0)
            ->whereRaw('vld.qty_available <= products.alert_quantity')
            ->select(
                'products.id',
                'products.name',
                'products.alert_quantity',
                DB::raw('SUM(vld.qty_available) as qty_available'),
                DB::raw('MAX(products.alert_quantity * 2) as reorder_qty'),
                DB::raw('MAX(COALESCE(v.default_purchase_price, 0)) as purchase_price')
            )
            ->groupBy('products.id', 'products.name', 'products.alert_quantity')
            ->orderBy('qty_available')
            ->get();

        $items = [];
        $total_value = 0;

        foreach ($low_stock as $p) {
            $reorder_qty = max(1, ($p->alert_quantity * 2) - $p->qty_available);
            $line_value  = $reorder_qty * ($p->purchase_price ?? 0);
            $total_value += $line_value;

            $items[] = [
                'product_id'  => $p->id,
                'name'        => $p->name,
                'in_stock'    => (float)$p->qty_available,
                'alert_at'    => (float)$p->alert_quantity,
                'reorder_qty' => $reorder_qty,
                'unit_cost'   => (float)($p->purchase_price ?? 0),
                'line_value'  => round($line_value, 2),
            ];
        }

        return [
            'count'       => count($items),
            'total_value' => round($total_value, 2),
            'items'       => $items,
            'scanned_at'  => Carbon::now()->format('d M Y H:i'),
        ];
    }
}
