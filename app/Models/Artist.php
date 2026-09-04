<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'art_name',
        'bio',
        'profile_picture',
        'social_links',
        'contact_info',
        'verification_status',
        'verification_documents',
    ];

    protected $casts = [
        'social_links' => 'array',
        'verification_documents' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function releases()
    {
        return $this->hasMany(Release::class);
    }
}
