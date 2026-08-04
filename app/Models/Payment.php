<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'release_id',
        'subscription_id',
        'amount',
        'status',
        'payment_method',
        'transaction_reference',
        'invoice_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function release()
    {
        return $this->belongsTo(Release::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
