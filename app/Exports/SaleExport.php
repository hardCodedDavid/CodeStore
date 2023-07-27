<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SaleExport implements FromArray, WithHeadings
{
    private $type;
    private $from;
    private $to;

    public function __construct($type, $from, $to)
    {
        $this->type = $type;
        $this->from = $from;
        $this->to = $to;
    }

    public function array(): array
    {
        switch ($this->type){
            case 'online':
                $sales = Sale::query()->latest()->with('items')->where('type', 'online');
                break;
            case 'offline':
                $sales = Sale::query()->latest()->with('items')->where('type', 'offline');
                break;
            default:
                $sales = Sale::query()->latest()->with('items');
        }
        $sales = $sales->whereDate('created_at', '>=', $this->from)
                        ->whereDate('created_at', '<=', $this->to)
                        ->get();
        return $sales->map(function($sale){
            return [
                'code' => $sale['code'] ,
                'name' => $sale['customer_name'] ,
                'email' => $sale['customer_email'] ?? 'Not set',
                'phone' => $sale['customer_phone'] ?? 'Not set',
                'address' => $sale['customer_address'] ?? 'Not set',
                'products' => count($sale['items']),
                'quantity' => $sale->getTotalQuantity(),
                'sub_total' => number_format($sale->getSubTotal()),
                'shipping' => $sale['shipping_fee'] ? number_format($sale['shipping_fee']) : '---',
                'additional' => $sale['additional_fee'] ? number_format($sale['shipping_fee']) : '---',
                'total' => number_format($sale->getTotal()),
                'date' => $sale['date']->format('M d, Y')
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'code',
            'customer name',
            'customer email',
            'customer phone',
            'customer address',
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
