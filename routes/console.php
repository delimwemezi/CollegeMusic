<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('make:admin {email=delfinusideusdedith@gmail.com} {password=deli@123!}', function ($email, $password) {
    $user = \App\Models\User::updateOrCreate(
        ['email' => $email],
        [
            'name' => 'Delfinusi Deusdedith',
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]
    );

    $this->info("Admin user [{$user->email}] successfully created/updated.");
})->purpose('Create or update an admin user');

