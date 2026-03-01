<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false; // Based on migration which lacks timestamps for this table if any (Wait, migration 2026_03_01_000004 has no timestamps let me assume no timestamps for now, or I'll check if it fails)

    public function values()
    {
        return $this->hasMany(AttributeValue::class, 'group_id')->orderBy('display_order');
    }
}
