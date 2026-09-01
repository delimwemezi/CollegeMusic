<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Artist;
use App\Models\Release;
use App\Models\Track;
use App\Models\ReleaseStore;
use App\Models\Royalty;
use App\Models\Withdrawal;
use App\Models\Payment;
use App\Models\AuditLog;
use App\Models\Subscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admins
      $admin = User::updateOrCreate(
    ['email' => 'delfinusideusdedith@gmail.com'],
    [
        'name' => 'Delfinusi Deusdedith',
        'phone' => '0744908978',
        'password' => Hash::make('Deli@123!'),
        'role' => 'admin',
        'status' => 'active',
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]
     );

        $systemAdmin = User::updateOrCreate(
            ['email' => 'admin@collegemusic.com'],
            [
                'name' => 'System Administrator',
                'phone' => '+1112223333',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'system_setup',
            'description' => 'System administrator account generated during initialization.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Console Seeder'
        ]);

    }   
}    