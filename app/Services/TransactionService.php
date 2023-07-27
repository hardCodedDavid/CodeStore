<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Variation;
use App\Models\ItemNumber;

class TransactionService 
{
    public function getPurchaseInvoice(Purchase $purchase) : array
    {
        $variations = Variation::query()->with(['items'])->get();
        return 
        [
            'purchase' => $purchase,
            // 'variations' => $variations
        ];
    }

    public function getSaleInvoice(Sale $sale) : array
    {
        $variations = Variation::query()->with(['items'])->get();
        return 
        [
            'sale' => $sale,
            // 'variations' => $variations
        ];
    }

    public function storePurchase(array $data): object
    {

        // Find supplier
        $supplier = Supplier::find(request('supplier'));

        // Store purchase
        $data = request()->only('date', 'note', 'shipping_fee', 'additional_fee');
        $data['code'] = Purchase::getCode();
        $data['created_by'] = auth('admin')->id();
        $purchase = $supplier->purchases()->create($data);

        // Store purchase products
        $variations = Variation::all();
        foreach (request('products') as $product) {
            // Get the current product
            $currentProduct = Product::find($product['product']);
            $itemNumbers = $product['item_numbers'];
            if ($currentProduct) {
                // Store purchase items
                $item = $purchase->items()->create([
                    'product_id' => $product['product'],
                    'brand_id' => $product['brand'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price']
                ]);

                // Assign selected variation items to purchase item
                foreach ($currentProduct->variationItems()->get() as $currentVariationItem) {
                    foreach ($variations as $variation) {
                        $currentId = $product[$variation['name']];
                        if ($currentId && $currentId == $currentVariationItem['id']){
                            $item->variationItems()->attach($currentId);
                        }
                    }
                }
                $currentProduct->update(['quantity' => $currentProduct['quantity'] + abs($product['quantity'])]);
                foreach ($itemNumbers as $itemNumber)
                    $currentProduct->itemNumbers()->create(['purchase_item_id' => $item['id'], 'no' => $itemNumber]);
            }
        }

        return $purchase;
    }

    public function updatePurchase(Purchase $purchase, array $data): object
    {
        // Store purchase
        $data = request()->only('date', 'note', 'shipping_fee', 'additional_fee');
        if (!$purchase['updated_by']) {
            $data['updated_by'] = auth('admin')->id();
            $data['updated_date'] = now();
        }
        $data['last_updated_by'] = auth('admin')->id();
        $purchase->update($data);

        // Remove all purchase items and respective variation items
        foreach($purchase->items()->get() as $item) {
            $item->variationItems()->sync([]);
            $item->delete();
        }

        // Store purchase products
        $variations = Variation::all();
        foreach (request('products') as $key => $product) {
            // Get the current product
            $currentProduct = Product::find($product['product']);
            $oldItemNumbers = $product['old_item_numbers'] ?? [];
            $newItemNumbers = $product['item_numbers'] ?? [];
            if ($currentProduct) {
                // Store purchase items
                $item = $purchase->items()->create([
                    'product_id' => $product['product'],
                    'brand_id' => $product['brand'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price']
                ]);

                // Assign selected variation items to purchase item
                foreach ($currentProduct->variationItems()->get() as $currentVariationItem) {
                    foreach ($variations as $variation) {
                        $currentId = $product[$variation['name']];
                        if ($currentId && $currentId == $currentVariationItem['id']){
                            $item->variationItems()->attach($currentId);
                        }
                    }
                }

                if ($oldItemNumbers)
                    foreach ($oldItemNumbers as $number) {
                        $itemNumber = ItemNumber::find($number['id']);
                        if ($itemNumber && $itemNumber['product_id'] == $currentProduct['id'])
                            $itemNumber->update(['purchase_item_id' => $item['id'], 'no' => $number['no']]);
                    }

                if ($newItemNumbers)
                    foreach ($newItemNumbers as $number)
                        if ($number)
                            $currentProduct->itemNumbers()->create(['purchase_item_id' => $item['id'], 'no' => $number]);
            }
            // if ($key == 0)
            //     $currentProduct->update(['quantity' => $product['quantity']]);
            // else
            //     $currentProduct->update(['quantity' => $currentProduct['quantity'] + $product['quantity']]);
        }

        return $purchase;
    }

    public function storeSale(array $data): object 
    {
        // Store sale
        $data = request()->only('customer_name', 'customer_email', 'customer_phone', 'customer_address', 'date', 'note', 'shipping_fee', 'additional_fee');
        $data['code'] = Sale::getCode();
        $data['created_by'] = auth('admin')->id();
        $sale = Sale::create($data);

        // Store sale products
        $variations = Variation::all();
        foreach (request('products') as $product) {
            // Get the current product
            $currentProduct = Product::find($product['product']);
            $itemNumbers = $product['item_numbers'];
            $numbers = [];
            if ($currentProduct) {
                // Store sale items
                $item = $sale->items()->create([
                    'product_id' => $product['product'],
                    'brand_id' => $product['brand'],
                    'quantity' => $product['quantity'],
                    'price' => $currentProduct['sell_price'],
                    'profit' => $currentProduct->getProfit() * $product['quantity']
                ]);

                foreach ($itemNumbers as $itemNumber) {
                    $number = ItemNumber::find($itemNumber);
                    if ($number) {
                        $numbers[] = [$number['id'] => $number['no']];
                        $number->update(['sale_item_id' => $item['id'], 'status' => 'sold', 'date_sold' => now()]);
                    }
                }

                $item->update(['item_numbers' => json_encode($numbers)]);

                // Assign selected variation items to sale item
                foreach ($currentProduct->variationItems()->get() as $currentVariationItem) {
                    foreach ($variations as $variation) {
                        $currentId = $product[$variation['name']];
                        if ($currentId && $currentId == $currentVariationItem['id']){
                            $item->variationItems()->attach($currentId);
                        }
                    }
                }
                $currentProduct->update(['quantity' => $currentProduct['quantity'] - abs($product['quantity'])]);
            }
        }
        // self::sendSaleSMS($sale);

        return $sale;
    }

    public function updateSale(Sale $sale, array $data): object 
    {
        // Store purchase
        $data = request()->only('customer_name', 'customer_email', 'customer_phone', 'customer_address', 'date', 'note', 'shipping_fee', 'additional_fee');
        if (!$sale['updated_by']) {
            $data['updated_by'] = auth('admin')->id();
            $data['updated_date'] = now();
        }
        $data['last_updated_by'] = auth('admin')->id();
        $sale->update($data);

        $qtyArr = [];
        // Remove all sale items and respective variation items
        foreach($sale->items()->get() as $item) {
            $item->variationItems()->sync([]);
            $qtyArr[$item['product_id'].'_'.$item['id']] = $item['quantity'];
            $item->delete();
        }

        // Store sale products
        $variations = Variation::all();
        foreach (request('products') as $product) {
            // Get the current product
            $currentProduct = Product::find($product['product']);
            $newItemNumbers = $product['item_numbers'] ?? [];
            $oldItemNumbers = $product['old_item_numbers'] ?? [];
            $numbers = [];
            if ($currentProduct) {
                // Store sale items
                $item = $sale->items()->create([
                    'product_id' => $product['product'],
                    'brand_id' => $product['brand'],
                    'quantity' => $product['quantity'],
                    'price' => $currentProduct['sell_price'],
                    'profit' => $currentProduct->getProfit() * $product['quantity']
                ]);

                foreach ($oldItemNumbers as $itemNumber) {
                    $number = ItemNumber::find($itemNumber['id']);
                    if ($number) {
                        $oldItemId = $number['sale_item_id'];
                        $numbers[] = [$number['id'] => $number['no']];
                        $number->update(['sale_item_id' => $item['id']]);
                    }
                }

                foreach ($newItemNumbers as $itemNumber) {
                    $number = ItemNumber::find($itemNumber);
                    if ($number) {
                        $numbers[] = [$number['id'] => $number['no']];
                        $number->update(['sale_item_id' => $item['id'], 'status' => 'sold', 'date_sold' => now()]);
                    }
                }

                $item->update(['item_numbers' => json_encode($numbers)]);

                // Assign selected variation items to sale item
                foreach ($currentProduct->variationItems()->get() as $currentVariationItem) {
                    foreach ($variations as $variation) {
                        $currentId = $product[$variation['name']];
                        if ($currentId && $currentId == $currentVariationItem['id']){
                            $item->variationItems()->attach($currentId);
                        }
                    }
                }
                $newQty = $currentProduct['quantity'] - ($product['quantity'] - $qtyArr[$currentProduct['id'].'_'.$oldItemId]);
                $newQty = $newQty < 0 ? 0 : $newQty;
                $currentProduct->update(['quantity' => $newQty]);
            }
        }

        return $sale;
    }

    public function deletePurchase(Purchase $purchase): bool
    {
        // Remove all purchase items and respective variation items
        foreach($purchase->items()->get() as $item) {
            $product = $item->product;
            $item->itemNumbers()->delete();
            $item->variationItems()->sync([]);
            $product->update(['quantity' => $product['quantity'] - $item['quantity']]);
            $item->delete();
        }
        $purchase->delete();

        return true;
    }

    public function deleteSale(Sale $sale): bool 
    {
        // Remove all purchase items and respective variation items
        foreach($sale->items()->get() as $item) {
            $product = $item->product;
            $itemNumbers = json_decode($item['item_numbers'] ?? '', true);
            foreach ($itemNumbers as $itemNumber) {
                $number = ItemNumber::find(array_keys($itemNumber)[0]);
                if ($number)
                    $number->update(['sale_item_id' => null, 'status' => 'available', 'date_sold' => null]);
            }
            $product->update(['quantity' => $product['quantity'] + $item['quantity']]);
            $item->variationItems()->sync([]);
            $item->delete();
        }
        $sale->delete();

        return true;
    }
}