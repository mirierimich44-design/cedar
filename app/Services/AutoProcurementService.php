<?php

namespace App\Services;

use App\Product;
use App\ProductVariation;
use App\PurchaseOrder;
use App\PurchaseOrderLine;
use Illuminate\Support\Facades\DB;

class AutoProcurementService
{
    /**
     * Run reorder checks and generate POs.
     */
    public function run()
    {
        $lowStockProducts = Product::whereRaw('qty_available <= alert_quantity')->get();

        foreach ($lowStockProducts as $product) {
            // Formula: (Demand * LeadTime) + SafetyStock
            // For MVP: Target = (AlertQty * 2) - CurrentStock
            $reorderQty = ($product->alert_quantity * 2) - $product->qty_available;
            
            if ($reorderQty > 0) {
                $this->createDraftPO($product, $reorderQty);
            }
        }
    }

    private function createDraftPO($product, $qty)
    {
        $po = PurchaseOrder::create([
            'business_id' => $product->business_id,
            'ref_no' => 'AUTO-' . time(),
            'status' => 'draft',
            'created_by' => 1 // Should be a system user
        ]);

        PurchaseOrderLine::create([
            'purchase_order_id' => $po->id,
            'product_id' => $product->id,
            'quantity' => $qty,
            'pp_without_discount' => $product->purchase_price
        ]);
    }
}
