<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    protected $casts = [
        'name' => 'json',
    ];

    public function parent()
    {
        return $this->belongsTo(TemplateCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(TemplateCategory::class, 'parent_id');
    }

    public function templates()
    {
        return $this->hasMany(Template::class, 'category_id');
    }
}