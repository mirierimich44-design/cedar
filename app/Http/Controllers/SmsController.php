<?php

namespace App\Http\Controllers;

use App\Contact;
use App\CustomerGroup;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmsController extends Controller
{
    protected $util;

    public function __construct(Util $util)
    {
        $this->util = $util;
    }

    /**
     * Show the Send SMS form.
     */
    public function sendForm()
    {
        if (!auth()->user()->can('send_notifications')) {
            abort(403, 'Unauthorized');
        }

        $business_id = request()->session()->get('user.business_id');

        $customer_groups = CustomerGroup::where('business_id', $business_id)->pluck('name', 'id');

        return view('sms.send', compact('customer_groups'));
    }

    /**
     * Process and send SMS.
     */
    public function send(Request $request)
    {
        if (!auth()->user()->can('send_notifications')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'message' => 'required|string|max:1600',
        ]);

        $business_id = request()->session()->get('user.business_id');
        $sms_settings = request()->session()->get('business.sms_settings');

        $recipient_type = $request->input('recipient_type', 'manual');
        $message = $request->input('message');

        $numbers = [];

        if ($recipient_type === 'manual') {
            $raw = $request->input('mobile_numbers', '');
            $numbers = array_filter(array_map('trim', explode(',', $raw)));
        } elseif ($recipient_type === 'all_customers') {
            $numbers = Contact::where('business_id', $business_id)
                ->where('type', 'customer')
                ->whereNotNull('mobile')
                ->where('mobile', '!=', '')
                ->pluck('mobile')
                ->toArray();
        } elseif ($recipient_type === 'group') {
            $group_id = $request->input('customer_group_id');
            $numbers = Contact::where('business_id', $business_id)
                ->where('customer_group_id', $group_id)
                ->whereNotNull('mobile')
                ->where('mobile', '!=', '')
                ->pluck('mobile')
                ->toArray();
        } elseif ($recipient_type === 'all_suppliers') {
            $numbers = Contact::where('business_id', $business_id)
                ->where('type', 'supplier')
                ->whereNotNull('mobile')
                ->where('mobile', '!=', '')
                ->pluck('mobile')
                ->toArray();
        }

        if (empty($numbers)) {
            return back()->with('status', ['success' => false, 'msg' => 'No recipients found.']);
        }

        $sent = 0;
        $failed = 0;

        foreach (array_chunk($numbers, 50) as $batch) {
            try {
                $data = [
                    'sms_settings'  => $sms_settings,
                    'mobile_number' => implode(',', $batch),
                    'sms_body'      => $message,
                ];
                $this->util->sendSms($data);
                $sent += count($batch);
            } catch (\Exception $e) {
                \Log::emergency('SMS send error: ' . $e->getMessage());
                $failed += count($batch);
            }
        }

        // Log the send
        try {
            DB::table('sms_logs')->insert([
                'business_id'    => $business_id,
                'sent_by'        => auth()->id(),
                'recipient_type' => $recipient_type,
                'total_sent'     => $sent,
                'total_failed'   => $failed,
                'message'        => $message,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        } catch (\Exception $e) {
            // Table may not exist yet; silently ignore
        }

        $msg = "SMS sent to {$sent} recipient(s).";
        if ($failed > 0) {
            $msg .= " {$failed} failed.";
        }

        return back()->with('status', ['success' => true, 'msg' => $msg]);
    }

    /**
     * Show SMS history.
     */
    public function history()
    {
        if (!auth()->user()->can('send_notifications')) {
            abort(403, 'Unauthorized');
        }

        $business_id = request()->session()->get('user.business_id');

        try {
            $logs = DB::table('sms_logs')
                ->where('business_id', $business_id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } catch (\Exception $e) {
            $logs = null;
        }

        return view('sms.history', compact('logs'));
    }

    /**
     * Show automated SMS settings (links to notification templates).
     */
    public function automated()
    {
        if (!auth()->user()->can('send_notifications')) {
            abort(403, 'Unauthorized');
        }

        return view('sms.automated');
    }
}
