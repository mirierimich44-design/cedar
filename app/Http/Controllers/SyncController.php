<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Contact;
use App\TransactionPayment;
use App\System;
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

        $cloud_url = System::getProperty('cloud_sync_url') ?? '';
        $api_token = System::getProperty('cloud_sync_token') ?? '';

        return view('sync.index', compact('unsynced_transactions', 'unsynced_payments', 'unsynced_contacts', 'cloud_url', 'api_token'));
    }

    /**
     * Save cloud sync settings
     */
    public function saveSettings(Request $request)
    {
        System::addProperty('cloud_sync_url', $request->cloud_sync_url);
        System::addProperty('cloud_sync_token', $request->cloud_sync_token);

        return redirect()->back()->with('status', ['success' => 1, 'msg' => 'Cloud Sync settings updated successfully!']);
    }

    /**
     * Push local data to the cloud.
     */
    public function pushToCloud(Request $request)
    {
        $cloud_url = System::getProperty('cloud_sync_url');
        $api_token = System::getProperty('cloud_sync_token');

        if (empty($cloud_url) || empty($api_token)) {
            return response()->json(['success' => false, 'msg' => 'Cloud Sync not configured. Please enter settings below.']);
        }

        try {
            DB::beginTransaction();

            // 1. Collect data
            $transactions = DB::table('transactions')->where('is_synced', 0)->limit(50)->get();
            $payments = DB::table('transaction_payments')->where('is_synced', 0)->whereIn('transaction_id', $transactions->pluck('id'))->get();
            $contacts = DB::table('contacts')->where('is_synced', 0)->get();

            if ($transactions->isEmpty() && $contacts->isEmpty() && $payments->isEmpty()) {
                return response()->json(['success' => true, 'msg' => 'Everything is already synced.', 'remaining' => 0]);
            }

            // 2. Send to cloud
            $response = Http::withToken($api_token)->post(rtrim($cloud_url, '/') . '/api/sync/receive', [
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

                // Check remaining
                $remaining = DB::table('transactions')->where('is_synced', 0)->count() + 
                             DB::table('contacts')->where('is_synced', 0)->count();

                DB::commit();
                return response()->json(['success' => true, 'msg' => 'Batch synced successfully.', 'remaining' => $remaining]);
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
        // SECURITY CHECK: Verify the Bearer Token matches our secret
        $token = $request->bearerToken();
        $expected_token = System::getProperty('cloud_sync_token');
        
        if (empty($expected_token) || $token !== $expected_token) {
            return response()->json(['success' => false, 'error' => 'Unauthorized sync attempt.'], 401);
        }

        $data = $request->all();
        
        try {
            DB::beginTransaction();
            
            // Map Local IDs to Cloud IDs so relationships don't break
            $contact_id_map = [];
            $transaction_id_map = [];
            
            // Upsert Contacts
            foreach($data['contacts'] ?? [] as $contact_data) {
                $local_contact_id = $contact_data['id'];
                unset($contact_data['id']); // Let remote server generate its own ID or update existing
                
                $contact_data['is_synced'] = 1;

                DB::table('contacts')->updateOrInsert(
                    ['contact_id' => $contact_data['contact_id']],
                    (array)$contact_data
                );

                // Find the real cloud ID
                $cloud_contact = DB::table('contacts')->where('contact_id', $contact_data['contact_id'])->first();
                if ($cloud_contact) {
                    $contact_id_map[$local_contact_id] = $cloud_contact->id;
                }
            }

            // Upsert Transactions
            foreach($data['transactions'] ?? [] as $transaction_data) {
                $local_txn_id = $transaction_data['id'];
                unset($transaction_data['id']);
                
                // If contact was mapped, update it
                if (isset($contact_id_map[$transaction_data['contact_id']])) {
                    $transaction_data['contact_id'] = $contact_id_map[$transaction_data['contact_id']];
                }

                $transaction_data['is_synced'] = 1;
                
                DB::table('transactions')->updateOrInsert(
                    ['ref_no' => $transaction_data['ref_no']],
                    (array)$transaction_data
                );
                
                // Find the real cloud ID
                $cloud_txn = DB::table('transactions')->where('ref_no', $transaction_data['ref_no'])->first();
                if ($cloud_txn) {
                    $transaction_id_map[$local_txn_id] = $cloud_txn->id;
                }
            }

            // Upsert Payments
            foreach($data['payments'] ?? [] as $payment_data) {
                unset($payment_data['id']);
                
                // Map the foreign key
                if (isset($transaction_id_map[$payment_data['transaction_id']])) {
                    $payment_data['transaction_id'] = $transaction_id_map[$payment_data['transaction_id']];
                }
                
                $payment_data['is_synced'] = 1;

                DB::table('transaction_payments')->updateOrInsert(
                    ['payment_ref_no' => $payment_data['payment_ref_no']],
                    (array)$payment_data
                );
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
