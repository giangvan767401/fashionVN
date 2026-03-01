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
}
