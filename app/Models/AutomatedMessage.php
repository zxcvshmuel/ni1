<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomatedMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'content',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'name' => 'json',
        'content' => 'json',
        'settings' => 'json',
        'is_active' => 'boolean',
    ];

    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class, 'message_id');
    }
}