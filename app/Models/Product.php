<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'discount_percent' => 'integer',
        'base_price'       => 'float',
        'sale_price'       => 'float',
    ];

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_products')->withPivot('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories')->withPivot('is_primary');
    }

    public function getTotalQuantityAttribute()
    {
        return $this->variants->sum('quantity');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->where('status', 'approved')->avg('rating') ?? 0, 1);
    }

    public function getPrimaryImageUrlAttribute()
    {
        $img = $this->images->firstWhere('is_primary', true) ?? $this->images->first();
        return $img ? $img->url : asset('user/img/default-product.jpg');
    }

    /**
     * Kiểm tra sản phẩm có đang được giảm giá không.
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->discount_percent > 0;
    }

    /**
     * Trả về giá hiển thị sau khi giảm (nếu có). 
     * Nếu không giảm giá, trả về base_price.
     */
    public function getEffectivePriceAttribute(): float
    {
        if ($this->discount_percent > 0) {
            return round($this->base_price * (1 - $this->discount_percent / 100));
        }
        return $this->sale_price ?? $this->base_price;
    }
}

