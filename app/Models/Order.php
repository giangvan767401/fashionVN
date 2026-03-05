<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'coupon_id',
        'payment_method_id',
        'shipping_method_id',
        'ship_name',
        'ship_phone',
        'ship_province',
        'ship_district',
        'ship_ward',
        'ship_address',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'status',
        'payment_status',
        'cancel_reason',
        'admin_note',
        'customer_note',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
