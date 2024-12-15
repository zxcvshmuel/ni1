<?php

namespace App\Models;

use Spatie\ActivityLog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Effect extends Model
{
    use HasFactory, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'type', 'settings', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function invitations(): BelongsToMany
    {
        return $this->belongsToMany(Invitation::class, 'invitation_effects')
            ->withPivot('settings')
            ->withTimestamps();
    }

    public function getNameAttribute($value): array
    {
        $name = json_decode($value, true);
        return [
            'he' => $name['he'] ?? '',
            'en' => $name['en'] ?? '',
        ];
    }

    public function getDescriptionAttribute($value): array
    {
        $description = json_decode($value, true);
        return [
            'he' => $description['he'] ?? '',
            'en' => $description['en'] ?? '',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        // Cleanup effect settings from invitations when deleted
        static::deleting(function (Effect $effect) {
            $effect->invitations()->detach();
        });
    }
}