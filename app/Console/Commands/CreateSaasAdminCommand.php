<?php

use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Facades\Hash;

class CreateSaasAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:create-admin';

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
        $username = 'saas_admin';
        $password = 'admin123'; 

        $user = User::where('username', $username)->first();

        if (!$user) {
            $user = User::create([
                'surname' => 'SaaS',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'username' => $username,
                'email' => 'admin@apexpos.co.ke',
                'password' => Hash::make($password),
                'language' => 'en',
                'allow_login' => 1
            ]);
            $this->info("User created successfully.");
        } else {
            $user->password = Hash::make($password);
            $user->save();
            $this->info("User password updated.");
        }

        $this->info("Username: $username");
        $this->info("Password: $password");

        return 0;
    }
}
