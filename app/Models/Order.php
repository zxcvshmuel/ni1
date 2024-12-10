<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'credit_package_id',
        'amount',
        'credits',
        'status',
        'payment_id',
        'payment_method',
        'invoice_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'credits' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creditPackage()
    {
        return $this->belongsTo(CreditPackage::class);
    }
}