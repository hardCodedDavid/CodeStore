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
}