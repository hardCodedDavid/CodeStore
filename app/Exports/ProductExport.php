<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromArray, WithHeadings
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
            case 'listed':
                $products = Product::query()->latest()->with(['brands', 'variationItems'])->where('is_listed', 1);
                break;
            case 'unlisted':
                $products = Product::query()->latest()->with(['brands', 'variationItems'])->where('is_listed', 0);
                break;
            default:
                $products = Product::query()->latest()->with(['brands', 'variationItems']);
        }
        $products = $products->whereDate('created_at', '>=', $this->from)
                        ->whereDate('created_at', '<=', $this->to)
                        ->get();
        return $products->map(function($product){
            $variations = $brands = '';
            $itemCount = count($product->variationItems);
            foreach ($product->variationItems as $key=>$item) {
                $variations .= $item['name'];
                if ($key < ($itemCount - 1))
                    $variations .= ' - ';
            }
            $brandCount = count($product->brands);
            foreach ($product->brands as $key=>$brand) {
                $brands .= $brand['name'];
                if ($key < ($brandCount - 1))
                    $brands .= ' - ';
            }
            return [
                'code' => $product['code'],
                'name' => $product['name'] ?? 'Not Set',
                'buy_price' => $product['buy_price'] ?? 'Not Set',
                'sell_price' => $product['sell_price'] ?? 'Not Set',
                'discount' => $product['discount'] ?? 'Not Set',
                'sku' => $product['sku'] ?? 'Not Set',
                'in_stock' => $product['in_stock'] == 1 ? 'In stock' : 'Out of stock',
                'quantity' => $product['quantity'] ?? 'Not Set',
                'weight' => $product['weight'] ?? 'Not Set',
                'brands' => $brands ?? 'Not Set',
                'variations' => $variations ?? 'Not Set',
                'status' => $product['is_listed'] == 1 ? 'Featured' : "Not Featured"
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'code',
            'name',
            'buy price',
            'sell price',
            'discount',
            'sku',
            'stock',
            'available quantity',
            'weight',
            'variations',
            'status',
        ];
    }
}
