<?php

namespace App\Models;

use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['date'];

    public function getTotalQuantity(): int
    {
        $qty = 0;
        foreach ($this->items()->get() as $item)
            $qty += $item->itemNumbers()->count();
        return $qty;
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getCreatedBy()
    {
        $admin = Admin::find($this->attributes['created_by']);
        return $admin ? explode(' ', $admin->name)[0] : null;
    }

    public function getUpdatedBy()
    {
        $admin = Admin::find($this->attributes['updated_by']);
        return $admin ? explode(' ', $admin->name)[0] : null;
    }

    public function getLastUpdatedBy()
    {
        $admin = Admin::find($this->attributes['last_updated_by']);
        return $admin ? explode(' ', $admin->name)[0] : null;
    }

    public function getSubTotal()
    {
        $sum = 0;
        foreach ($this->items()->get() as $item)
            $sum += $item->itemNumbers()->count() * $item['price'];
        return $sum;
    }

    public function getProfit()
    {
        return $this->items()->sum('profit');
    }

    public function getTotal()
    {
        $shipping = $this->shipping_fee ?? 0;
        $additional = $this->additional_fee ?? 0;
        return $this->getSubTotal() + $shipping + $additional;
    }

    public static function getCode(): string
    {
        $last_item = Sale::query()->latest()->first();
        if ($last_item) $num = $last_item['id'] + 1;
        else $num = 1;
        return self::generateUniqueCode($num);
    }

    protected static function generateUniqueCode($num): string
    {
        while (strlen($num) < 6){
            $num = '0'.$num;
        }
        return 'SL'.$num;
    }
}
