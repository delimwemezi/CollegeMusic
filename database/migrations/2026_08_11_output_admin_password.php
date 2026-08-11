<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Find admin user
        $admin = DB::table('users')->where('role', 'admin')->first();
        
        if ($admin) {
            // Generate a readable password
            $newPassword = 'Admin' . substr(md5(uniqid()), 0, 12);
            $hashedPassword = Hash::make($newPassword);
            
            // Update the password
            DB::table('users')->where('id', $admin->id)->update([
                'password' => $hashedPassword
            ]);
            
            // Echo to console/logs
            echo "\n================================\n";
            echo "✅ ADMIN PASSWORD RESET SUCCESS\n";
            echo "================================\n";
            echo "Email: {$admin->email}\n";
            echo "Password: {$newPassword}\n";
            echo "================================\n\n";
        } else {
            echo "No admin user found in database\n";
        }
    }

    public function down(): void
    {
        // Cannot rollback
    }
};

