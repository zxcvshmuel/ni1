<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Template extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'html_structure',
        'css_styles',
        'category_id',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'settings' => 'json',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TemplateCategory::class, 'category_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnails')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->fit(Manipulations::FIT_CROP, 400, 240)
                    ->optimize();

                $this->addMediaConversion('preview')
                    ->fit(Manipulations::FIT_CROP, 800, 480)
                    ->optimize();
            });
    }

    protected static function boot()
    {
        parent::boot();

        // Add cleanup on delete
        static::deleting(function (Template $template) {
            $template->clearMediaCollection('thumbnails');
        });
    }
}