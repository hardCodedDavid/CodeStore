<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Auth\StatefulGuard;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;


class ProductService
{
    
    public function __construct(protected ProductRepository $repository) 
    {
        //
    }

    public function all() : object 
    {
        // $product = $this->repository->getAll(page: get_page(), per_page: get_per_page());

        $product = QueryBuilder::for(Product::class)->allowedIncludes(['categories', 'categories.subcategories'])->paginate(20);
                    
        return $product;
    }

    public function addProduct(array $data): object
    {
        $data = request()->only('name', 'description', 'full_description', 'buy_price', 'sell_price', 'discount', 'sku', 'weight', 'note');
        $data['code'] = Product::getCode();
        $data['in_stock'] = request('in_stock') == 'instock';
        $data['is_listed'] = request('feature') == 'feature';
        $data['created_by'] = auth('admin')->id();

        $product = $this->repository->create($data);

        $product->categories()->attach(request('categories'));

        return $this->repository->find($product['id']);
    }

    public function showProduct(Product $product) : object 
    {
        $product->load('categories');
        
        return $product;
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

        $item->categories()->sync(request('categories'));


        return $this->repository->find($item['id']);
    }

    public function deleteProduct(Product $product): bool
    {
        return $this->repository->destroy($product);
    }
}