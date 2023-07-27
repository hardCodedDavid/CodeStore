<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseExport implements FromArray, WithHeadings
{
    private $from;
    private $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function array(): array
    {
        $purchases = Purchase::query()
                        ->latest()
                        ->with('items')
                        ->whereDate('created_at', '>=', $this->from)
                        ->whereDate('created_at', '<=', $this->to)
                        ->get();
        return $purchases->map(function($purchase){
            return [
                'code' => $purchase['code'],
                'supplier' => $purchase['supplier']['name'],
                'products' => count($purchase['items']),
                'quantity' => $purchase->getTotalQuantity(),
                'sub_total' => number_format($purchase->getSubTotal()),
                'shipping' => $purchase['shipping_fee'] ? number_format($purchase['shipping_fee']) : '---',
                'additional' => $purchase['additional_fee'] ? number_format($purchase['shipping_fee']) : '---',
                'total' => number_format($purchase->getTotal()),
                'date' => $purchase['date']->format('M d, Y')
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'code',
            'supplier',
            'total products',
            'total quantity',
            'sub total',
            'shipping fee',
            'additional fee',
            'total',
            'date'
        ];
    }
}
