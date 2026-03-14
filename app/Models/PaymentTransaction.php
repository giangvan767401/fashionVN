<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'transaction_code',
        'gateway',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'paid_at',
        'created_at',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'paid_at'          => 'datetime',
        'created_at'       => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
