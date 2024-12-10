<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist',
        'file_path',
        'duration',
        'is_active',
        'plays_count',
    ];

    protected $casts = [
        'duration' => 'integer',
        'is_active' => 'boolean',
        'plays_count' => 'integer',
    ];

    public function invitations()
    {
        return $this->belongsToMany(Invitation::class, 'invitation_songs')
            ->withTimestamps();
    }
}