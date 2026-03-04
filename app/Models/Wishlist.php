<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'added_at';

    protected $fillable = ['user_id', 'variant_id'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
