<?php

namespace Database\Seeders;

use App\Business;
use App\User;
use App\Utils\BusinessUtil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class CiataMallBusinessSeeder extends Seeder
{
    public function run()
    {
        // Prevent duplicate
        if (User::where('username', 'admin')->exists()) {
            $this->command->info('User "admin" already exists. Skipping.');
            return;
        }

        DB::beginTransaction();

        try {
            $businessUtil = app(BusinessUtil::class);

            // Create owner user
            $user = User::create([
                'first_name'  => 'Admin',
                'last_name'   => '',
                'username'    => 'admin',
                'email'       => 'admin@ciatamall.co.ke',
                'password'    => Hash::make('ciata01'),
                'language'    => 'en',
                'business_id' => 0, // temporary, updated below
            ]);

            // Create the business
            $business = $businessUtil->createNewBusiness([
                'name'               => 'CPL - CIATA MALL',
                'currency_id'        => 133, // KES - Kenyan shilling
                'start_date'         => now()->toDateString(),
                'time_zone'          => 'Africa/Nairobi',
                'fy_start_month'     => 1,
                'accounting_method'  => 'fifo',
                'owner_id'           => $user->id,
                'enabled_modules'    => ['purchases', 'add_sale', 'pos_sale', 'stock_transfers', 'stock_adjustment', 'expenses'],
            ]);

            // Link user to business
            $user->business_id = $business->id;
            $user->save();

            // Create default roles, walk-in customer, invoice scheme/layout
            $businessUtil->newBusinessDefaultResources($business->id, $user->id);

            // Add default business location
            $new_location = $businessUtil->addLocation($business->id, [
                'name'    => 'CPL - CIATA MALL',
                'country' => 'Kenya',
                'state'   => 'Nairobi',
                'city'    => 'Nairobi',
                'zip_code' => '00100',
                'landmark' => 'Nairobi',
            ]);

            // Create location permission
            Permission::firstOrCreate(['name' => 'location.' . $new_location->id]);

            DB::commit();

            $this->command->info('Business "CPL - CIATA MALL" created successfully.');
            $this->command->info('Username: admin | Password: ciata01');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
