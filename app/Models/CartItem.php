<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    public const CREATED_AT = 'added_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'cart_id',
        'variant_id',
        'quantity',
        'unit_price',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
