<?php

namespace App\Console\Commands;

use App\Business;
use App\Contact;
use App\Utils\BusinessUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixWalkInCustomers extends Command
{
    protected $signature   = 'app:fix-walkin-customers';
    protected $description = 'Creates a Walk-In Customer (is_default=1) for every business that does not already have one';

    public function handle()
    {
        $businesses = Business::all();

        foreach ($businesses as $business) {
            $exists = Contact::where('business_id', $business->id)
                ->where('is_default', 1)
                ->whereIn('type', ['customer', 'both'])
                ->exists();

            if ($exists) {
                $this->line("Business #{$business->id} ({$business->name}): walk-in customer already exists — skipped.");
                continue;
            }

            // Generate a simple contact_id reference
            $count     = Contact::where('business_id', $business->id)->count() + 1;
            $contactId = 'WLK-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            // Make sure it's unique for this business
            while (Contact::where('business_id', $business->id)->where('contact_id', $contactId)->exists()) {
                $count++;
                $contactId = 'WLK-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }

            Contact::create([
                'business_id'  => $business->id,
                'type'         => 'customer',
                'name'         => 'Walk-In Customer',
                'created_by'   => $business->owner_id ?? 1,
                'is_default'   => 1,
                'contact_id'   => $contactId,
                'credit_limit' => 0,
            ]);

            $this->info("Business #{$business->id} ({$business->name}): walk-in customer CREATED ({$contactId}).");
        }

        $this->info('Done.');
        return 0;
    }
}
