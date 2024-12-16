<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'integer';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    protected $fillable = [
        'group',
        'name',
        'payload',
        'locked'
    ];

    protected $casts = [
        'payload' => 'json',
        'locked' => 'boolean'
    ];

    /**
     * Get the value stored in the settings payload
     */
    public function getValue()
    {
        $payload = $this->payload;
        return $payload['value'] ?? null;
    }

    /**
     * Set the value in the settings payload
     */
    public function setValue($value): self
    {
        $this->payload = ['value' => $value];
        return $this;
    }

    /**
     * Get a unique identifier for the setting
     */
    public function getKeyIdentifier(): string
    {
        return "{$this->group}.{$this->name}";
    }
}