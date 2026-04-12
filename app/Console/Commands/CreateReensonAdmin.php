<?php

namespace App\Console\Commands;

use App\Business;
use App\User;
use App\Utils\BusinessUtil;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class CreateReensonAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:create-reenson-admin 
                            {email=admin@reenson.co.ke : The email of the admin} 
                            {password=password123 : The password for the admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user linked to Reenson with all rights to the app (Self-healing permissions)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        // Explicitly setting the username as requested
        $username = 'admin'; 

        $this->info("Starting process... ensuring permissions exist first.");

        // MANUALLY ENSURE THE FAILING PERMISSIONS EXIST
        $requiredPermissions = [
            'sell.view', 'sell.create', 'sell.update', 'sell.delete', 
            'sell.payments', 'access_all_locations', 'view_cash_register', 
            'close_cash_register', 'dashboard.data'
        ];

        foreach ($requiredPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        
        // Clear Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::beginTransaction();

        try {
            $businessUtil = app(BusinessUtil::class);

            // 1. Check if the business exists, or create it.
            $business = Business::where('name', 'like', '%Reenson%')->first();

            if (!$business) {
                $this->info("Business 'Reenson' not found. Creating it...");
                
                $currency = \App\Currency::where('code', 'KES')->first();
                $currency_id = $currency ? $currency->id : 1; 

                $tempUser = User::create([
                    'first_name'  => 'Admin',
                    'last_name'   => 'Reenson',
                    'username'    => 'temp_' . time(),
                    'email'       => 'temp_' . time() . '@reenson.co.ke',
                    'password'    => Hash::make(uniqid()),
                    'language'    => 'en',
                    'status'      => 'active',
                    'allow_login' => 1,
                ]);

                $business = $businessUtil->createNewBusiness([
                    'name'               => 'Reenson',
                    'currency_id'        => $currency_id,
                    'start_date'         => now()->toDateString(),
                    'time_zone'          => 'Africa/Nairobi',
                    'fy_start_month'     => 1,
                    'accounting_method'  => 'fifo',
                    'owner_id'           => $tempUser->id,
                    'enabled_modules'    => ['purchases', 'add_sale', 'pos_sale', 'stock_transfers', 'stock_adjustment', 'expenses'],
                    'is_active'          => 1,
                ]);

                $businessUtil->newBusinessDefaultResources($business->id, $tempUser->id);

                $businessUtil->addLocation($business->id, [
                    'name'    => 'Reenson - Main',
                    'country' => 'Kenya',
                    'state'   => 'Nairobi',
                    'city'    => 'Nairobi',
                    'zip_code' => '',
                    'landmark' => '',
                ]);
            } else {
                $this->info("Found existing business. Ensuring it is active...");
                $business->is_active = 1;
                $business->save();
            }

            // 2. Create or Update the requested Admin User
            $user = User::where('email', $email)->orWhere('username', $username)->first();

            if ($user) {
                $this->info("Updating existing user...");
                $user->password = Hash::make($password);
                $user->business_id = $business->id;
                $user->username = $username;
                $user->email = $email;
                $user->status = 'active';
                $user->allow_login = 1;
                $user->save();
            } else {
                $this->info("Creating new user...");
                $user = User::create([
                    'first_name'  => 'Super',
                    'last_name'   => 'Admin',
                    'username'    => $username,
                    'email'       => $email,
                    'password'    => Hash::make($password),
                    'language'    => 'en',
                    'business_id' => $business->id,
                    'status'      => 'active',
                    'allow_login' => 1,
                ]);
            }

            // 3. Assign Admin Role
            $roleName = 'Admin#' . $business->id;
            $role = Role::where('name', $roleName)->where('business_id', $business->id)->first();
            
            if (!$role) {
                $role = Role::create([
                    'name' => $roleName,
                    'business_id' => $business->id,
                    'guard_name' => 'web', 
                    'is_default' => 1
                ]);
            }

            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }

            DB::commit();

            $this->info('=====================================');
            $this->info('Success! Admin user ready.');
            $this->info('Business: Reenson');
            $this->info('Login Field (Username): ' . $username);
            $this->info('Login Field (Email): ' . $email);
            $this->info('Password: ' . $password);
            $this->info('=====================================');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
