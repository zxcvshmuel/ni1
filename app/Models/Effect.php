<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Effect extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'settings' => 'json',
        'is_active' => 'boolean',
    ];

    public function invitations()
    {
        return $this->belongsToMany(Invitation::class, 'invitation_effects')
            ->withPivot('settings')
            ->withTimestamps();
    }
}