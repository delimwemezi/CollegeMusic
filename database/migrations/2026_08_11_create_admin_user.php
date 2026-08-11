<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Check if admin exists
        $admin = DB::table('users')->where('role', 'admin')->first();
        
        if (!$admin) {
            // Generate admin credentials
            $email = 'admin@collegemusic.app';
            $newPassword = 'Admin' . substr(md5(uniqid()), 0, 12);
            
            // Create admin user
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => $email,
                'password' => Hash::make($newPassword),
                'role' => 'admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Output to logs
            echo "\n================================\n";
            echo "✅ ADMIN USER CREATED\n";
            echo "================================\n";
            echo "Email: {$email}\n";
            echo "Password: {$newPassword}\n";
            echo "================================\n\n";
        } else {
            // Admin exists, reset password
            $newPassword = 'Admin' . substr(md5(uniqid()), 0, 12);
            
            DB::table('users')->where('id', $admin->id)->update([
                'password' => Hash::make($newPassword),
                'updated_at' => now(),
            ]);
            
            echo "\n================================\n";
            echo "✅ ADMIN PASSWORD RESET\n";
            echo "================================\n";
            echo "Email: {$admin->email}\n";
            echo "Password: {$newPassword}\n";
            echo "================================\n\n";
        }
    }

    public function down(): void
    {
        // Cannot rollback
    }
};

