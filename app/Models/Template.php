<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

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

    public function category()
    {
        return $this->belongsTo(TemplateCategory::class, 'category_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }
}