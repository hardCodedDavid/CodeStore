<?php

namespace App\Models;

use App\Models\Sale;
use App\Models\Brand;
use App\Models\Product;
use App\Models\VariationItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SaleItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getQuantityAttribute(): int
    {
        return $this->itemNumbers()->count();
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variationItems(): BelongsToMany
    {
        return $this->belongsToMany(VariationItem::class);
    }

    public function itemNumbers(): HasMany
    {
        return $this->hasMany(ItemNumber::class);
    }

    public function getVariationItemsIdToArray(): array
    {
        $arr = [];
        foreach ($this->variationItems()->get() as $item) {
            $arr[] = $item['id'];
        }
        return $arr;
    }
}
