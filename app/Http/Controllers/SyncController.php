<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Contact;
use App\TransactionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    /**
     * Display the sync dashboard.
     */
    public function index()
    {
        $unsynced_transactions = DB::table('transactions')->where('is_synced', 0)->count();
        $unsynced_payments = DB::table('transaction_payments')->where('is_synced', 0)->count();
        $unsynced_contacts = DB::table('contacts')->where('is_synced', 0)->count();

        return view('sync.index', compact('unsynced_transactions', 'unsynced_payments', 'unsynced_contacts'));
    }

    /**
     * Push local data to the cloud.
     */
    public function pushToCloud(Request $request)
    {
        $cloud_url = config('app.cloud_sync_url');
        $api_token = config('app.cloud_sync_token');

        if (empty($cloud_url) || empty($api_token)) {
            return response()->json(['success' => false, 'msg' => 'Cloud Sync not configured.']);
        }

        try {
            DB::beginTransaction();

            // 1. Collect data
            $transactions = DB::table('transactions')->where('is_synced', 0)->limit(50)->get();
            $payments = DB::table('transaction_payments')->where('is_synced', 0)->whereIn('transaction_id', $transactions->pluck('id'))->get();
            $contacts = DB::table('contacts')->where('is_synced', 0)->get();

            if ($transactions->isEmpty() && $contacts->isEmpty()) {
                return response()->json(['success' => true, 'msg' => 'Everything is already synced.']);
            }

            // 2. Send to cloud
            $response = Http::withToken($api_token)->post($cloud_url . '/api/sync/receive', [
                'transactions' => $transactions,
                'payments' => $payments,
                'contacts' => $contacts,
                'business_id' => session('business.id')
            ]);

            if ($response->successful()) {
                // 3. Mark as synced
                DB::table('transactions')->whereIn('id', $transactions->pluck('id'))->update(['is_synced' => 1]);
                DB::table('transaction_payments')->whereIn('id', $payments->pluck('id'))->update(['is_synced' => 1]);
                DB::table('contacts')->whereIn('id', $contacts->pluck('id'))->update(['is_synced' => 1]);

                DB::commit();
                return response()->json(['success' => true, 'msg' => 'Sync completed successfully.']);
            }

            DB::rollBack();
            return response()->json(['success' => false, 'msg' => 'Cloud server error: ' . $response->body()]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Receive data from local instance (Endpoint for the Hosting server).
     */
    public function receiveFromLocal(Request $request)
    {
        // This method would be used on the hosting server
        $data = $request->all();
        
        try {
            DB::beginTransaction();
            
            // Logic to upsert transactions, payments, and contacts
            // Since this is a POS, we need to handle reference numbers to avoid duplicates
            
            foreach($data['contacts'] ?? [] as $contact_data) {
                unset($contact_data['id']); // Let remote server generate its own ID
                DB::table('contacts')->updateOrInsert(
                    ['contact_id' => $contact_data['contact_id']],
                    (array)$contact_data
                );
            }

            foreach($data['transactions'] ?? [] as $transaction_data) {
                $local_id = $transaction_data['id'];
                unset($transaction_data['id']);
                
                DB::table('transactions')->updateOrInsert(
                    ['ref_no' => $transaction_data['ref_no']],
                    (array)$transaction_data
                );
                
                // Note: In a real scenario, we'd need to map the local transaction ID 
                // to the remote transaction ID for payments.
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
