<?php

namespace App\Utils;

use App\Product;
use App\Transaction;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DigitaxService
{
    protected $api_key;
    protected $base_url;
    protected $client;

    public function __construct()
    {
        $this->base_url = 'https://api.digitax.tech/ke/v2';
        $this->client = new Client([
            'base_uri' => $this->base_url,
            'timeout'  => 30.0,
        ]);
    }

    /**
     * Set API Key dynamically from business settings
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
        return $this;
    }

    protected function getHeaders()
    {
        return [
            'X-API-Key' => $this->api_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Register a product as an item in eTIMS
     */
    public function registerItem(Product $product)
    {
        try {
            $payload = [
                'item_name' => $product->name,
                'item_code' => $product->sku,
                'tax_category' => $product->etims_tax_category ?? 'A',
                'unit_of_measure' => $product->etims_uom ?? 'U',
                'package_unit_of_measure' => 'U',
                'quantity_per_package' => 1,
                'is_stockable' => $product->enable_stock ? true : false,
            ];

            $response = $this->client->post('items', [
                'headers' => $this->getHeaders(),
                'json' => $payload
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
                $product->etims_item_id = $result['item_id'] ?? $product->sku;
                $product->etims_synced = 1;
                $product->save();
                return ['success' => true, 'data' => $result];
            }

            return ['success' => false, 'error' => 'Unexpected status code: ' . $response->getStatusCode()];

        } catch (\Exception $e) {
            Log::error('Digitax Item Sync Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create a sale (invoice) in eTIMS
     */
    public function createSale(Transaction $transaction)
    {
        try {
            $transaction->load(['sell_lines', 'sell_lines.product', 'contact']);

            $items = [];
            foreach ($transaction->sell_lines as $line) {
                $items[] = [
                    'item_id' => $line->product->etims_item_id ?? $line->product->sku,
                    'quantity' => (float)$line->quantity,
                    'unit_price' => (float)$line->unit_price_inc_tax,
                    'discount_amount' => (float)$line->line_discount_amount ?? 0,
                ];
            }

            $payload = [
                'customer_pin' => $transaction->contact->tax_number ?? 'P000000000A',
                'customer_name' => $transaction->contact->name ?? 'Cash Customer',
                'transaction_type' => 'SALE',
                'receipt_type' => 'NORMAL',
                'payment_method' => $this->mapPaymentMethod($transaction),
                'items' => $items,
                'external_reference' => $transaction->invoice_no
            ];

            $response = $this->client->post('sales', [
                'headers' => $this->getHeaders(),
                'json' => $payload
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
                $transaction->etims_invoice_number = $result['invoice_number'] ?? null;
                $transaction->etims_qr_url = $result['qr_code_url'] ?? null;
                $transaction->etims_signature = $result['signature'] ?? null;
                $transaction->etims_sync_status = 'success';
                $transaction->etims_synced_at = now();
                $transaction->save();

                return ['success' => true, 'data' => $result];
            }

            return ['success' => false, 'error' => 'Unexpected status code: ' . $response->getStatusCode()];

        } catch (\Exception $e) {
            Log::error('Digitax Sale Sync Error: ' . $e->getMessage());
            
            $transaction->etims_sync_status = 'failed';
            $transaction->etims_sync_error = $e->getMessage();
            $transaction->save();

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create a purchase (incoming invoice) in eTIMS
     */
    public function createPurchase(Transaction $transaction)
    {
        try {
            $transaction->load(['purchase_lines', 'purchase_lines.product', 'contact']);

            $items = [];
            foreach ($transaction->purchase_lines as $line) {
                $items[] = [
                    'item_id'         => $line->product->etims_item_id ?? $line->product->sku,
                    'quantity'        => (float) $line->quantity,
                    'unit_price'      => (float) $line->purchase_price_inc_tax,
                    'discount_amount' => 0,
                ];
            }

            $payload = [
                'supplier_pin'       => $transaction->contact->tax_number ?? 'P000000000A',
                'supplier_name'      => $transaction->contact->name ?? 'Supplier',
                'transaction_type'   => 'PURCHASE',
                'receipt_type'       => 'NORMAL',
                'payment_method'     => 'CASH',
                'items'              => $items,
                'external_reference' => $transaction->ref_no,
            ];

            $response = $this->client->post('purchases', [
                'headers' => $this->getHeaders(),
                'json'    => $payload,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (in_array($response->getStatusCode(), [200, 201])) {
                $transaction->etims_invoice_number = $result['invoice_number'] ?? null;
                $transaction->etims_qr_url         = $result['qr_code_url'] ?? null;
                $transaction->etims_signature      = $result['signature'] ?? null;
                $transaction->etims_sync_status    = 'success';
                $transaction->etims_synced_at      = now();
                $transaction->save();

                return ['success' => true, 'data' => $result];
            }

            return ['success' => false, 'error' => 'Unexpected status code: ' . $response->getStatusCode()];

        } catch (\Exception $e) {
            Log::error('Digitax Purchase Sync Error: ' . $e->getMessage());

            $transaction->etims_sync_status = 'failed';
            $transaction->etims_sync_error  = $e->getMessage();
            $transaction->save();

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function mapPaymentMethod($transaction)
    {
        // Default mapping, can be expanded based on project payment methods
        return 'CASH';
    }
}
