<?php

namespace Database\Seeders;

use App\Business;
use App\SaasFeature;
use App\SaasInvoice;
use App\SaasSubscription;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

/**
 * Creates a ready-to-use test business that mirrors what the SaaS onboarding
 * flow (SaasPricingController@submitOrder) produces, without going through
 * the web form. Safe to re-run — it skips if the email already exists.
 *
 *   php artisan db:seed --class=TestBusinessSeeder
 */
class TestBusinessSeeder extends Seeder
{
    public function run()
    {
        $email    = 'test@apexpos.co.ke';
        $password = 'test1234';

        if (User::where('email', $email)->exists()) {
            $this->command->warn("User {$email} already exists. Delete it first to reseed.");
            return;
        }

        $businessUtil = new BusinessUtil;
        $moduleUtil   = new ModuleUtil;

        // Resolve KES currency id dynamically (varies between installs)
        $kesId = DB::table('currencies')->where('code', 'KES')->value('id');
        if (!$kesId) {
            $kesId = DB::table('currencies')->value('id'); // fallback to first available
            $this->command->warn("KES currency not found — using currency id {$kesId} as fallback.");
        }

        DB::beginTransaction();
        try {
            // 1. Owner user
            $user = User::create_user([
                'surname'    => '',
                'first_name' => 'Apex',
                'last_name'  => 'Tester',
                'username'   => 'apextester',
                'email'      => $email,
                'password'   => $password,
                'language'   => 'en',
            ]);

            // 2. Business (KE defaults)
            $business = $businessUtil->createNewBusiness([
                'name'              => 'Apex Test Clinic',
                'currency_id'       => $kesId,
                'start_date'        => now()->toDateString(),
                'time_zone'         => 'Africa/Nairobi',
                'fy_start_month'    => 1,
                'accounting_method' => 'fifo',
                'owner_id'          => $user->id,
                'enabled_modules'   => ['purchases', 'add_sale', 'pos_sale', 'stock_transfers', 'stock_adjustment', 'expenses'],
            ]);

            // 3. Link
            $user->business_id = $business->id;
            $user->save();

            // 4. Default resources (Admin role, walk-in customer, invoice scheme/layout)
            $businessUtil->newBusinessDefaultResources($business->id, $user->id);

            // 5. Default location
            $location = $businessUtil->addLocation($business->id, [
                'name'     => 'Apex Test Clinic',
                'country'  => 'Kenya',
                'state'    => 'Nairobi',
                'city'     => 'Nairobi',
                'zip_code' => '00100',
                'landmark' => 'Nairobi CBD',
                'mobile'   => '+254700000000',
            ]);
            $locPerm = Permission::firstOrCreate(['name' => 'location.' . $location->id]);
            $user->givePermissionTo($locPerm);
            $user->givePermissionTo(Permission::firstOrCreate(['name' => 'access_all_locations']));

            // 6. Fire module hooks
            try {
                $moduleUtil->getModuleData('after_business_created', ['business' => $business]);
            } catch (\Throwable $e) {
                // non-fatal
            }

            // 7. Active 30-day subscription with ALL features (test account gets everything)
            $features = SaasFeature::where('is_active', true)->orderBy('sort_order')->get();
            $total    = $features->sum(fn($f) => $f->priceFor('monthly'));

            $sub = SaasSubscription::create([
                'business_id'   => $business->id,
                'billing_cycle' => 'monthly',
                'hosting_type'  => 'cloud',
                'total_amount'  => $total,
                'status'        => 'active',
                'starts_at'     => now(),
                'ends_at'       => now()->addDays(30),
                'grace_ends_at' => now()->addDays(37),
            ]);
            foreach ($features as $f) {
                $sub->features()->attach($f->id, ['price_locked' => $f->priceFor('monthly')]);
            }

            // 8. Paid invoice so the subscription looks real
            SaasInvoice::create([
                'invoice_no'        => SaasInvoice::generateNumber(),
                'business_id'       => $business->id,
                'subscription_id'   => $sub->id,
                'amount'            => $total,
                'currency'          => 'KES',
                'status'            => 'paid',
                'type'              => 'subscription',
                'paid_at'           => now(),
                'payment_method'    => 'manual',
                'payment_reference' => 'TEST-SEED',
            ]);

            DB::commit();

            $this->command->info('');
            $this->command->info('======================================================');
            $this->command->info(' TEST BUSINESS CREATED');
            $this->command->info('======================================================');
            $this->command->info(" Login URL   : https://apexpos.co.ke/login");
            $this->command->info(" Email       : {$email}");
            $this->command->info(" Username    : apextester");
            $this->command->info(" Password    : {$password}");
            $this->command->info(" Business ID : {$business->id}");
            $this->command->info(" Features    : " . $features->pluck('name')->implode(', '));
            $this->command->info(" Subscription: active, expires " . $sub->ends_at->format('d M Y'));
            $this->command->info('======================================================');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('Seed failed: ' . $e->getMessage());
            $this->command->error('File: ' . $e->getFile() . ':' . $e->getLine());
        }
    }
}
