<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Royalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'track_id',
        'platform',
        'amount',
        'country',
        'date',
        'streams_count',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }
}
