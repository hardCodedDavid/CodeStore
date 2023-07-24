<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function banners()
    {
        return $this->hasMany(CategoryBanner::class);
    }
}
