<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = false; // The migration doesn't have timestamps by default

    protected $fillable = [
        'order_id',
        'variant_id',
        'product_name',
        'variant_label',
        'sku',
        'quantity',
        'unit_price',
        'discount_amount',
        'total_price',
        'image_url',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
