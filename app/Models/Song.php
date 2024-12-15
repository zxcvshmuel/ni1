<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Spatie\ActivityLog\LogOptions;
use Spatie\ActivityLog\Traits\LogsActivity;

class Song extends Model
{
    use HasFactory, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'artist', 'file_path', 'duration', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function invitations(): BelongsToMany
    {
        return $this->belongsToMany(Invitation::class, 'invitation_songs')
            ->withTimestamps();
    }

    public function incrementPlaysCount(): void
    {
        $this->increment('plays_count');
    }

    public function getFileUrl(): string
    {
        return Storage::disk('local')->url($this->file_path);
    }

    public function getDurationFormatted(): string
    {
        return gmdate('i:s', $this->duration);
    }

    protected static function boot()
    {
        parent::boot();

        // Delete the file when the song is deleted
        static::deleting(function (Song $song) {
            if ($song->file_path && Storage::disk('local')->exists($song->file_path)) {
                Storage::disk('local')->delete($song->file_path);
            }

            // Detach from all invitations
            $song->invitations()->detach();
        });
    }
}