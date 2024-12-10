<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsvpResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'name',
        'email',
        'phone',
        'guests_count',
        'status',
        'preferences',
        'notes',
    ];

    protected $casts = [
        'guests_count' => 'integer',
        'preferences' => 'json',
    ];

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }
}