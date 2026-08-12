<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'verification_code',
        'notification_preferences',
        'payout_account',
        'email_verified_at',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
            'payout_account' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isArtist(): bool
    {
        return $this->role === 'artist';
    }

    public function isRecordLabel(): bool
    {
        return $this->role === 'record_label';
    }

    public function isDistributor(): bool
    {
        return $this->role === 'distributor';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function artist()
    {
        return $this->hasOne(Artist::class);
    }

    public function artists()
    {
        return $this->hasMany(Artist::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
