<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'title',
        'type',
        'cover_image',
        'genre',
        'language',
        'release_date',
        'copyright_info',
        'scheduling_type',
        'distribution_status',
        'rejection_reason',
        'billing_status',
        'price_paid',
    ];

    protected $casts = [
        'release_date' => 'date',
        'price_paid' => 'decimal:2',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function stores()
    {
        return $this->hasMany(ReleaseStore::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
