<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false;
    
    // In migration: $table->timestamp('created_at')->useCurrent();
    const UPDATED_AT = null;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        if (str_starts_with($value, 'user/img') || str_starts_with($value, 'images/products')) {
            return asset($value);
        }

        return asset('storage/' . $value);
    }
}
