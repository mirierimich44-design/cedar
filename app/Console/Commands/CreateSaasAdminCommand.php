<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CreateSaasAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:create-admin {email=admin@apexpos.co.ke} {password=admin123} {--grant= : Grant Superadmin role to an existing user by email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the default Super Admin user for the SaaS module';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // --grant=some@email.com just promotes an existing user without creating saas_admin
        if ($grantEmail = $this->option('grant')) {
            $user = User::where('email', $grantEmail)->first();
            if (! $user) {
                $this->error("No user found with email: $grantEmail");
                return 1;
            }
            $this->ensureSuperadminRole($user);
            $this->info("Superadmin role granted to: {$user->email} (username: {$user->username})");
            return 0;
        }

        $email = $this->argument('email');
        $password = $this->argument('password');
        $username = 'saas_admin';

        // Search only by username — do NOT use orWhere('email') which could
        // accidentally find and overwrite a real business-owner account.
        $user = User::where('username', $username)->first();

        // A SaaS super admin shouldn't normally need a business, but the base LoginController 
        // checks `$user->business->is_active`. We'll assign it to the first active business
        // or temporarily disable foreign key checks if no business exists
        $first_business = \App\Business::where('is_active', 1)->first();
        $business_id = $first_business ? $first_business->id : null;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        if (!$user) {
            $user = User::create([
                'surname' => 'SaaS',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'language' => 'en',
                'allow_login' => 1,
                'status' => 'active',
                'business_id' => $business_id
            ]);
            $this->info("SaaS Super Admin created successfully.");
        } else {
            $user->password = Hash::make($password);
            $user->email = $email;
            $user->status = 'active';
            $user->allow_login = 1;
            $user->business_id = $business_id;
            $user->save();
            $this->info("SaaS Super Admin updated successfully.");
        }

        $this->ensureSuperadminRole($user);

        $this->info("Username: $username");
        $this->info("Email: $email");
        $this->info("Password: $password");

        return 0;
    }

    private function ensureSuperadminRole(User $user): void
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $role = Role::where('name', 'Superadmin')->where('guard_name', 'web')->first();
            if (! $role) {
                $roleId = DB::table('roles')->insertGetId([
                    'name'        => 'Superadmin',
                    'guard_name'  => 'web',
                    'business_id' => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
                $role = Role::find($roleId);
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            if ($role && ! $user->hasRole('Superadmin')) {
                $user->assignRole($role);
            }
            $this->info("Role: Superadmin assigned.");
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->warn("Could not assign Spatie role: " . $e->getMessage());
        }
    }
}

