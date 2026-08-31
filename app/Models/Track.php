<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'release_id',
        'title',
        'artist_name',
        'genre',
        'composer',
        'songwriter',
        'isrc',
        'audio_file',
        'duration',
        'streams_count',
        'downloads_count',
    ];

    public function release()
    {
        return $this->belongsTo(Release::class);
    }

    public function royalties()
    {
        return $this->hasMany(Royalty::class);
    }
}
