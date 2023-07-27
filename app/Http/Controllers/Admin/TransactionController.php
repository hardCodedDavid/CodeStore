<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\ItemNumber;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Http\Requests\Sale\StoreRequest;
use App\Http\Requests\Purchase\UpdateRequest;
use App\Http\Requests\Purchase\PurchaseRequest;
use App\Http\Requests\Sale\UpdateRequest as SaleUpdateRequest;

class TransactionController extends Controller
{
    public function __construct(protected TransactionService $service)
    {
        //
    }

    public function puchaseInvoice(Purchase $purchase): JsonResponse
    {
        $data = $this->service->getPurchaseInvoice($purchase);

        return $this->success(message: 'Data fetched successfully', data: compact('data'));
    }

    public function saleInvoice(Sale $sale): JsonResponse
    {
        $data = $this->service->getSaleInvoice($sale);

        return $this->success(message: 'Data fetched successfully', data: compact('data'));
    }

    public function storePurchase(PurchaseRequest $request): JsonResponse
    {
        $numbers = [];
        $errors = [];
        foreach (request('products') as $key => $product)
            foreach ($product['item_numbers'] as $curKey => $number)
                $numbers[$number][] = $key.$curKey;

        foreach (request('products') as $key => $product)
            foreach ($product['item_numbers'] as $curKey => $number)
                if (count($numbers[$number]) > 1)
                    $errors['products.' . $key . '.item_numbers.' . $curKey] = 'The item number must be unique';


        if (count($errors) > 0)
            return $this->failure('An error occured!!', details: $errors);

        // Find supplier
        $supplier = Supplier::find(request('supplier'));
        if (!$supplier) {
            return $this->failure('An error occured!!', details: 'Supplier not found');
        }
        
        $data = $this->service->storePurchase($request->validated());

        return $this->success(message: 'Data created successfully', data: compact('data'));
    }

    public function updatePurchase(Purchase $purchase, UpdateRequest $request): JsonResponse
    {
        $numbers = [];
        $oldNumbers = [];
        $newErrors = [];
        $oldErrors = [];
        foreach (request('products') as $key => $product) {
            if (isset($product['item_numbers']))
                foreach ($product['item_numbers'] as $curKey => $number)
                    $numbers[$number][] = $key . $curKey;

            if (isset($product['old_item_numbers']))
                foreach ($product['old_item_numbers'] as $curKey => $number)
                    $oldNumbers[$number['no']][] = $key . $curKey;
        }

        foreach (request('products') as $key => $product) {
            if (isset($product['item_numbers']))
                foreach ($product['item_numbers'] as $curKey => $number)
                    if (count($numbers[$number]) > 1)
                        $newErrors['products.' . $key . '.item_numbers.' . $curKey] = 'The item number must be unique';

            if (isset($product['old_item_numbers']))
                foreach ($product['old_item_numbers'] as $curKey => $number)
                    if (count($oldNumbers[$number['no']]) > 1)
                        $oldErrors['products.' . $key . '.old_item_numbers.' . $curKey . '.no'] = 'The item number already exist';
        }
        $errors = array_merge($oldErrors, $newErrors);
        if (count($errors) > 0)
            return $this->failure('An error occured!!', details: $errors);

        // Find supplier
        $supplier = Supplier::find(request('supplier'));
        if (!$supplier) {
            return $this->failure('An error occured!!', details: 'Supplier not found');
        }

        $supplier = $this->service->updatePurchase($purchase, $request->validated());

        return $this->success(message: 'Data updated successfully', data: compact('supplier'));
    }

    public function storeSale(StoreRequest $request): JsonResponse
    {
        $errors = [];
        $numbers = [];
        foreach (request('products') as $key => $product)
            if (isset($product['item_numbers'])) {
                if (count($product['item_numbers']) != $product['quantity'])
                    $errors['products.' . $key . '.quantity'] = 'The item numbers must be equal to the quantity';
                foreach ($product['item_numbers'] as $curKey => $number)
                    $numbers[$number][] = $key.$curKey;
            }

        foreach (request('products') as $key => $product)
            if (isset($product['item_numbers']))
                foreach ($product['item_numbers'] as $number) {
                    if (count($numbers[$number]) > 1)
                        $errors['products.' . $key . '.item_numbers'] = 'Item number selected in multiple places';
                    $avail = ItemNumber::query()->where('id', $number)->where('status', 'available')->first();
                    if (!$avail)
                        $errors['products.' . $key . '.item_numbers'] = 'One or more item numbers not found';
                }

        if (count($errors) > 0)
            return $this->failure('An error occured!!', details: $errors);

        $data = $this->service->storeSale($request->validated());

        return $this->success(message: 'Data created successfully', data: compact('data'));
    }

    public function updateSale(Sale $sale, SaleUpdateRequest $request): JsonResponse
    {
        // Check if sale is online
        if ($sale['type'] == 'online') {
            return $this->failure('An error occured!!', details: 'Can\'t update online sale');
        }

        $numbers = [];
        $errors = [];
        foreach (request('products') as $key => $product) {
            if (isset($product['item_numbers']))
                foreach ($product['item_numbers'] as $curKey => $number)
                    $numbers[$number][] = $key . $curKey;

            if (count($product['item_numbers'] ?? []) + count($product['old_item_numbers'] ?? []) != $product['quantity'])
                $errors['products.' . $key . '.quantity'] = 'The item numbers must be equal to the quantity';
        }

        foreach (request('products') as $key => $product)
            if (isset($product['item_numbers']))
                foreach ($product['item_numbers'] as $number) {
                    if (count($numbers[$number]) > 1)
                        $errors['products.' . $key . '.item_numbers'] = 'Item number selected in multiple places';

                    $avail = ItemNumber::query()->where('id', $number)->where('status', 'available')->first();
                    if (!$avail)
                        $errors['products.' . $key . '.item_numbers'] = 'One or more item numbers not found';
                }


        if (count($errors) > 0)
            return $this->failure('An error occured!!', details: $errors);
        
        $data = $this->service->updateSale($sale, $request->validated());

        return $this->success(message: 'Data updated successfully', data: compact('data'));
    }

    public function destroyPurchase(Purchase $purchase): JsonResponse
    {
        foreach($purchase->items()->get() as $item) {
            $itemNumbers = $item->itemNumbers;
            foreach ($itemNumbers as $itemNumber)
                if ($itemNumber['status'] == 'sold')
                    return $this->failure(message: 'An error occured!!', details: 'Cannot delete purchase, one or more product has been sold from this purchase');
        }

        $this->service->deletePurchase($purchase);
        
        return $this->success('Data deleted successfully');
    }

    public function destroySale(Sale $sale): JsonResponse
    {
        // Check if sale is online
        if ($sale['type'] == 'online') {
            return $this->failure('An error occured!!', details: 'Can\'t delete online sale');
        }

        $this->service->deleteSale($sale);
        
        return $this->success('Data deleted successfully');
    }

    public function removeItemNumber($id): JsonResponse
    {
        $itemNumber = ItemNumber::find($id);
        if (!$itemNumber)
            return $this->failure('An error occured!!', details: 'Item number not found');
        if ($itemNumber->saleItem->itemNumbers()->count() <= 1)
            return $this->failure('An error occured!!', details: 'Can\'t remove item number, sale must have at least 1 item number');

        if ($itemNumber->update(['status' => 'available', 'date_sold' => null, 'sale_item_id' => null])) {
            $numbers = [];
            foreach (json_decode($itemNumber->saleItem->item_numbers, true) as $item)
                if ($itemNumber['id'] != array_keys($item)[0])
                    $numbers[] = [array_keys($item)[0] => array_values($item)[0]];
            $itemNumber->saleItem->update(['item_numbers' => json_encode($numbers)]);
            $itemNumber->product->update(['quantity' => $itemNumber->product->quantity + 1]);

            $data = [
                'success' => true,
                'msg' => 'Item number removed',
                'count' => $itemNumber->purchaseItem->itemNumbers()->count(),
                'itemNumbers' => $itemNumber->product->itemNumbers()->where('status', 'available')->get()->map(function ($item) { return ['id' => $item['id'], 'no' => $item['no']]; })
            ];

            return $this->success(message: 'Data updated successfully', data: compact('data'));
        }

        return $this->failure('An error occured!!', details: 'Item number could not be removed, try again');
    }

    public function deleteItemNumber($id): JsonResponse
    {
        $itemNumber = ItemNumber::find($id);
        if (!$itemNumber)
            return $this->failure('An error occured!!', details: 'Item number not found');

        if ($itemNumber->delete()) {
            $itemNumber->purchaseItem()->update(['quantity' => $itemNumber->purchaseItem->quantity - 1]);
            $itemNumber->product()->update(['quantity' => $itemNumber->product->quantity - 1]);
            return response()->json([
                'success' => true,
                'msg' => 'Item number deleted',
                'count' => $itemNumber->purchaseItem->itemNumbers()->count(),
                'quantity' => $itemNumber->purchaseItem->quantity - 1]);
        }

        return $this->failure('An error occured!!', details: 'Item number could not be deleted, try again');
    }
}
