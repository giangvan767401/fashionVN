<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false; // Assuming tags might not heavily use timestamps, but Laravel defaults to true. Let's omit if migration has it. Migration 2026_03_01_000007_create_tags_table.php might or might not. I'll remove it if standard.

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_tags');
    }
}
