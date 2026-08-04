<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReleaseStore extends Model
{
    use HasFactory;

    protected $table = 'release_store';

    protected $fillable = [
        'release_id',
        'store_name',
        'status',
    ];

    public function release()
    {
        return $this->belongsTo(Release::class);
    }
}
