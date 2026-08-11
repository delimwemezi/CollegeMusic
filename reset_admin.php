<?php
require 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Find admin user
$admin = User::where('role', 'admin')->first();

if (!$admin) {
    echo "❌ No admin user found in database\n";
    exit(1);
}

// Generate new password
$newPassword = bin2hex(random_bytes(8)); // 16 char hex password
$hashedPassword = Hash::make($newPassword);

// Update admin password
$admin->password = $hashedPassword;
$admin->save();

echo "✅ Admin password reset successfully!\n";
echo "Email: " . $admin->email . "\n";
echo "New Password: " . $newPassword . "\n";
echo "\nUse these credentials to log in to your admin panel.\n";
