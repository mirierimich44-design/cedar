<?php
use App\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

$username = 'saas_admin';
$password = 'admin123'; // User should change this after first login

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
    echo "User created successfully.\n";
} else {
    $user->password = Hash::make($password);
    $user->save();
    echo "User password updated.\n";
}

// Ensure the user has superadmin-like access. 
// In this system, permissions are often business-specific, 
// but for the SaaS admin routes, we just need a valid user.
// If your system uses Spatie roles, we can try to assign a role if it exists.

echo "Username: $username\n";
echo "Password: $password\n";
