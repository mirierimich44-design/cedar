<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateSaasAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:create-admin {email=admin@apexpos.co.ke} {password=admin123}';

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
        $email = $this->argument('email');
        $password = $this->argument('password');
        $username = 'saas_admin';

        $user = User::where('username', $username)->orWhere('email', $email)->first();

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

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("Username: $username");
        $this->info("Email: $email");
        $this->info("Password: $password");

        return 0;
    }
}

