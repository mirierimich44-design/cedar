<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\Contact;
use App\Product;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $business_id = $request->session()->get('user.business_id');
        $results = [];

        // Transactions (invoices)
        if (auth()->user()->can('sell.view') || auth()->user()->can('purchase.view')) {
            $transactions = Transaction::where('business_id', $business_id)
                ->where(function ($q2) use ($q) {
                    $q2->where('invoice_no', 'like', "%{$q}%")
                       ->orWhere('ref_no', 'like', "%{$q}%");
                })
                ->with('contact')
                ->limit(5)
                ->get();

            foreach ($transactions as $tx) {
                $type = $tx->type === 'sell' ? 'Sale' : ucfirst($tx->type);
                $results[] = [
                    'type'     => 'transaction',
                    'title'    => $tx->invoice_no ?: $tx->ref_no,
                    'subtitle' => $type . ' · ' . ($tx->contact ? $tx->contact->name : 'N/A'),
                    'meta'     => number_format($tx->final_total, 2),
                    'url'      => $tx->type === 'sell'
                        ? action([\App\Http\Controllers\SellController::class, 'show'], $tx->id)
                        : action([\App\Http\Controllers\PurchaseController::class, 'show'], $tx->id),
                ];
            }
        }

        // Contacts (customers/suppliers)
        if (auth()->user()->can('contact.view')) {
            $contacts = Contact::where('business_id', $business_id)
                ->where(function ($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                       ->orWhere('mobile', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%");
                })
                ->limit(5)
                ->get();

            foreach ($contacts as $c) {
                $results[] = [
                    'type'     => 'contact',
                    'title'    => $c->name,
                    'subtitle' => ucfirst($c->type) . ($c->mobile ? ' · ' . $c->mobile : ''),
                    'meta'     => null,
                    'url'      => action([\App\Http\Controllers\ContactController::class, 'show'], $c->id),
                ];
            }
        }

        // Products
        if (auth()->user()->can('product.view')) {
            $products = Product::where('business_id', $business_id)
                ->where(function ($q2) use ($q) {
                    $q2->where('name', 'like', "%{$q}%")
                       ->orWhere('sku', 'like', "%{$q}%");
                })
                ->limit(5)
                ->get();

            foreach ($products as $p) {
                $results[] = [
                    'type'     => 'product',
                    'title'    => $p->name,
                    'subtitle' => 'SKU: ' . ($p->sku ?? 'N/A'),
                    'meta'     => null,
                    'url'      => action([\App\Http\Controllers\ProductController::class, 'show'], $p->id),
                ];
            }
        }

        // Parcels (if module enabled)
        if (\Module::has('Parcel') && class_exists(\Modules\Parcel\Entities\Parcel::class)) {
            $parcels = \Modules\Parcel\Entities\Parcel::where('business_id', $business_id)
                ->where(function ($q2) use ($q) {
                    $q2->where('waybill_number', 'like', "%{$q}%")
                       ->orWhere('sender_name', 'like', "%{$q}%")
                       ->orWhere('recipient_name', 'like', "%{$q}%")
                       ->orWhere('sender_phone', 'like', "%{$q}%")
                       ->orWhere('recipient_phone', 'like', "%{$q}%");
                })
                ->limit(5)
                ->get();

            foreach ($parcels as $p) {
                $results[] = [
                    'type'     => 'parcel',
                    'title'    => $p->waybill_number,
                    'subtitle' => $p->sender_name . ' → ' . $p->recipient_name,
                    'meta'     => ucfirst($p->status),
                    'url'      => route('parcel.show', $p->id),
                ];
            }
        }

        return response()->json(['results' => $results]);
    }
}
