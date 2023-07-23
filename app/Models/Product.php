<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function getCode(): string
    {
        $last_item = static::query()->latest()->first();
        if ($last_item) $num = $last_item['id'] + 1;
        else $num = 1;
        return self::generateUniqueCode($num);
    }



    protected static function generateUniqueCode($num): string
    {
        while (strlen($num) < 6){
            $num = '0'.$num;
        }
        return 'PD'.$num;
    }

    public function getFormattedActualPrice(): string
    {
        return number_format($this->attributes['sell_price']);
    }

    public function getFormattedDiscountedPrice(): string
    {
        return number_format($this->attributes['sell_price'] - $this->attributes['discount']);
    }

    public function getDiscountedPrice()
    {
        return $this->attributes['sell_price'] - $this->attributes['discount'];
    }

    public function getDiscountedPercent(): float
    {
        return round((($this->attributes['discount']) / $this->attributes['sell_price']) * 100);
    }

    public function isDiscounted(): bool
    {
        return ($this->attributes['discount'] && $this->attributes['discount'] > 0);
    }

    public function isNew(): bool
    {
        return Carbon::parse($this->attributes['created_at'])->addDays(30)->gt(now());
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
}
