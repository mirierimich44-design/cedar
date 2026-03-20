<?php

namespace App\Jobs;

use App\Business;
use App\Contact;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppPaymentReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $business_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($business_id = null)
    {
        $this->business_id = $business_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $businesses = $this->business_id
            ? Business::where('id', $this->business_id)->get()
            : Business::all();

        foreach ($businesses as $business) {
            $this->processBusinessReminders($business);
        }
    }

    /**
     * Process payment reminders for a business
     */
    protected function processBusinessReminders(Business $business)
    {
        $whatsapp_settings = $business->whatsapp_settings;

        // Check if WhatsApp is enabled and payment reminders are enabled
        if (empty($whatsapp_settings['enabled']) || empty($whatsapp_settings['payment_reminder_enabled'])) {
            return;
        }

        // Check if it's a scheduled day
        if (!empty($whatsapp_settings['schedule_enabled'])) {
            $today = strtolower(Carbon::now()->format('l'));
            $scheduled_days = $whatsapp_settings['schedule_days'] ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

            if (!in_array($today, $scheduled_days)) {
                return;
            }
        }

        // Get all pending/partial payments
        $due_transactions = Transaction::where('business_id', $business->id)
            ->where('type', 'sell')
            ->where('payment_status', '!=', 'paid')
            ->whereNotNull('contact_id')
            ->with('contact')
            ->get();

        foreach ($due_transactions as $transaction) {
            $this->sendPaymentReminder($business, $transaction, $whatsapp_settings);
        }
    }

    /**
     * Send payment reminder to customer
     */
    protected function sendPaymentReminder(Business $business, Transaction $transaction, array $whatsapp_settings)
    {
        $contact = $transaction->contact;

        if (empty($contact) || empty($contact->mobile)) {
            return;
        }

        $template = $whatsapp_settings['payment_reminder_template'] ??
            "Hello {customer_name},\n\nThis is a friendly reminder about your pending payment.\n\nInvoice: {invoice_no}\nAmount Due: {amount_due}\nDue Date: {due_date}\n\nPlease contact us if you have any questions.";

        $amount_due = $transaction->final_total - $transaction->total_paid;

        $message = str_replace(
            ['{customer_name}', '{invoice_no}', '{amount_due}', '{due_date}', '{business_name}'],
            [
                $contact->name,
                $transaction->invoice_no,
                number_format($amount_due, 2),
                $transaction->due_date ? Carbon::parse($transaction->due_date)->format('d/m/Y') : 'N/A',
                $business->name
            ],
            $template
        );

        $this->sendWhatsAppMessage($whatsapp_settings, $contact->mobile, $message);
    }

    /**
     * Send WhatsApp message based on provider settings
     */
    protected function sendWhatsAppMessage(array $settings, string $to, string $message)
    {
        $provider = $settings['api_provider'] ?? 'meta';

        try {
            switch ($provider) {
                case 'meta':
                    $this->sendViaMeta($settings, $to, $message);
                    break;
                case 'twilio':
                    $this->sendViaTwilio($settings, $to, $message);
                    break;
                default:
                    $this->sendViaCustom($settings, $to, $message);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Send Error: ' . $e->getMessage());
        }
    }

    /**
     * Send via Meta WhatsApp Business API
     */
    protected function sendViaMeta(array $settings, string $to, string $message)
    {
        $url = rtrim($settings['api_url'] ?? 'https://graph.facebook.com/v17.0', '/')
            . '/' . $settings['phone_number_id'] . '/messages';

        $to = preg_replace('/[^0-9]/', '', $to);

        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $message]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $settings['access_token'],
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        Log::info('WhatsApp Meta Response: ' . $response);
    }

    /**
     * Send via Twilio
     */
    protected function sendViaTwilio(array $settings, string $to, string $message)
    {
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$settings['twilio_sid']}/Messages.json";

        $from = $settings['twilio_from'];
        if (strpos($from, 'whatsapp:') !== 0) {
            $from = 'whatsapp:' . $from;
        }
        if (strpos($to, 'whatsapp:') !== 0) {
            $to = 'whatsapp:+' . preg_replace('/[^0-9]/', '', $to);
        }

        $data = [
            'From' => $from,
            'To' => $to,
            'Body' => $message
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_USERPWD, $settings['twilio_sid'] . ':' . $settings['twilio_token']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        Log::info('WhatsApp Twilio Response: ' . $response);
    }

    /**
     * Send via custom API
     */
    protected function sendViaCustom(array $settings, string $to, string $message)
    {
        $url = $settings['custom_url'];

        $data = [
            'api_key' => $settings['custom_api_key'],
            'sender' => $settings['custom_sender'],
            'number' => $to,
            'message' => $message
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $settings['custom_api_key']
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        Log::info('WhatsApp Custom API Response: ' . $response);
    }
}
