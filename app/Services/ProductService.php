<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Auth\StatefulGuard;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;


class ProductService
{
    // public StatefulGuard $guard;
    
    public function __construct(protected ProductRepository $repository) 
    {
        //
    }

    public function all() : array 
    {
        $product = $this->repository->getAll(page: get_page(), per_page: get_per_page());
        return [
            'products' => $product,
            'meta' => [
                'page' => get_page(),
                'per_page' => get_per_page(),
                'total' => $product['total']
            ]
        ];
    }

    public function addProduct(array $data): object
    {
        $data = request()->only('name', 'description', 'full_description', 'buy_price', 'sell_price', 'discount', 'sku', 'weight', 'note');
        $data['code'] = Product::getCode();
        $data['in_stock'] = request('in_stock') == 'instock';
        $data['is_listed'] = request('feature') == 'feature';
        $data['created_by'] = auth('admin')->id();

        $product = $this->repository->create($data);
        return $this->repository->find($product['id']);
    }

    public function editProduct(Product $product, array $data): object
    {
        $data = request()->only('name', 'description', 'full_description', 'buy_price', 'sell_price', 'discount', 'sku', 'weight', 'note');
        
        $data['in_stock'] = request('in_stock') == 'instock';
        $data['is_listed'] = request('feature') == 'feature';
        if (!$product['updated_by']) {
            $data['updated_by'] = auth('admin')->id();
            $data['updated_date'] = now();
        }
        $data['last_updated_by'] = auth('admin')->id();

        $item = $this->repository->update($product, $data);

        return $this->repository->find($item['id']);
    }

    public function deleteProduct(Product $product): bool
    {
        return $this->repository->destroy($product);
    }
}