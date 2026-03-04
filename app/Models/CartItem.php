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

    public function getVariantLabelAttribute()
    {
        if (!$this->relationLoaded('variant') || !$this->variant) {
            return '';
        }

        $color = $this->variant->attributeValues->filter(function($val) {
            return mb_strtolower(optional($val->group)->name, 'UTF-8') === 'màu sắc';
        })->first();

        $size = $this->variant->attributeValues->filter(function($val) {
            return mb_strtolower(optional($val->group)->name, 'UTF-8') === 'kích thước';
        })->first();

        $attributes = [];
        if ($color) $attributes[] = 'MÀU: ' . mb_strtoupper($color->value, 'UTF-8');
        if ($size) $attributes[] = 'SIZE: ' . mb_strtoupper($size->value, 'UTF-8');

        return implode(' | ', $attributes);
    }
}
