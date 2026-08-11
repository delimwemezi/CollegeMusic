<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Find and reset admin password
        $admin = DB::table('users')->where('role', 'admin')->first();
        
        if ($admin) {
            $newPassword = bin2hex(random_bytes(8));
            DB::table('users')->where('id', $admin->id)->update([
                'password' => Hash::make($newPassword)
            ]);
            
            // Store the new password in a temporary file
            file_put_contents(storage_path('admin_reset.txt'), 
                "Admin Email: {$admin->email}\nNew Password: {$newPassword}\n");
        }
    }

    public function down(): void
    {
        // This migration cannot be rolled back
    }
};
