<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Business;
use App\Followup;
use App\Utils\NotificationUtil;
use Carbon\Carbon;

class SendFollowupReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:sendFollowupReminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send internal WhatsApp reminders for overdue or pending follow-ups';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $businesses = Business::all();
        $notificationUtil = new NotificationUtil();

        foreach ($businesses as $business) {
            $whatsapp_settings = $business->whatsapp_settings;

            if (!empty($whatsapp_settings['followup_notification_enabled'])) {
                $time_str = $whatsapp_settings['followup_notification_time'] ?? '09:00';
                $times = explode(',', $time_str);
                $times = array_map('trim', $times);

                $frequency = $whatsapp_settings['followup_notification_frequency'] ?? 'daily';
                
                $now = Carbon::now();
                $current_time = $now->format('H:i');
                
                // For simplicity, we check if current time matches any of the scheduled times. 
                // In production, you might want a tolerance window or a lock to prevent duplicate sends.
                if (!in_array($current_time, $times)) {
                    continue;
                }

                // For weekly frequency, check if it's Monday (default)
                if ($frequency == 'weekly' && !$now->isMonday()) {
                    continue;
                }

                $followups = Followup::where('business_id', $business->id)
                    ->where('status', 'pending')
                    ->with(['product', 'location'])
                    ->get();

                if ($followups->count() > 0) {
                    foreach ($followups as $followup) {
                        $customer_name = $followup->customer_name;
                        $product_name = $followup->product->name ?? 'N/A';
                        $followup_date = $followup->created_at->toFormattedDateString();
                        
                        $template = $whatsapp_settings['followup_notification_template'] ?? "Internal Reminder: Follow up with {customer_name} regarding their inquiry for {product_name}. Planned Date: {followup_date}";
                        
                        $message = str_replace(
                            ['{customer_name}', '{product_name}', '{business_name}', '{followup_date}'],
                            [$customer_name, $product_name, $business->name, $followup_date],
                            $template
                        );

                        // Recipient: Location mobile or Business Owner's contact number
                        $location = $followup->location;
                        $recipient_number = !empty($location->mobile) ? $location->mobile : ($business->owner ? $business->owner->contact_number : null);

                        if (!empty($recipient_number)) {
                            $notificationUtil->sendAfricasTalkingWhatsapp($recipient_number, $message);
                        }
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
