<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'slug',
        'title',
        'event_date',
        'event_type',
        'venue_name',
        'venue_address',
        'venue_latitude',
        'venue_longitude',
        'content',
        'settings',
        'is_active',
        'views_count',
        'expires_at',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'content' => 'json',
        'settings' => 'json',
        'is_active' => 'boolean',
        'views_count' => 'integer',
        'expires_at' => 'datetime',
        'venue_latitude' => 'decimal:8',
        'venue_longitude' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function songs()
    {
        return $this->belongsToMany(Song::class, 'invitation_songs')
            ->withTimestamps();
    }

    public function effects()
    {
        return $this->belongsToMany(Effect::class, 'invitation_effects')
            ->withPivot('settings')
            ->withTimestamps();
    }

    public function rsvpResponses()
    {
        return $this->hasMany(RsvpResponse::class);
    }

    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class);
    }
}