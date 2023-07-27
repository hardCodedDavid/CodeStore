<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory, UUID;

    protected $guarded = [];

    public function purchases(){
        return $this->hasMany(Purchase::class);
    }

    public function getTotalTransactions()
    {
        $total = 0;
        foreach ($this->purchases as $purchase) {
            $total += $purchase->getSubTotal();
        }
        return $total;
    }
}
