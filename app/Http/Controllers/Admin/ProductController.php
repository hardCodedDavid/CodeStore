<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service)
    {

    }
    
    public function index(): JsonResponse
    {
        $data = $this->service->all();

        return $this->success(message: 'Product fetched successfully', data: compact('data'));
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $product = $this->service->addProduct($request->validated());

        return $this->success(message: 'Product created successfully', data: compact('product'));
    }

    public function show(Product $product): JsonResponse
    {
        $product = $this->service->showProduct($product);

        return $this->success(message: 'Product fetched successfully', data: compact('product'));
    }

    public function update(Product $product, UpdateRequest $request): JsonResponse
    {
        $product = $this->service->editProduct($product, $request->validated());

        return $this->success('Product updated successfully', data: compact('product'));
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->service->deleteProduct($product);

        return $this->success('Product deleted successfully');
    }
}
