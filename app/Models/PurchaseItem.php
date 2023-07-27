<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function itemNumbers()
    {
        return $this->hasMany(ItemNumber::class);
    }

    public function variationItems()
    {
        return $this->belongsToMany(VariationItem::class);
    }

    public function getVariationItemsIdToArray()
    {
        $arr = [];
        foreach ($this->variationItems()->get() as $item) {
            $arr[] = $item['id'];
        }
        return $arr;
    }

    public function getQuantityAttribute(): int
    {
        return $this->itemNumbers()->count();
    }
}
