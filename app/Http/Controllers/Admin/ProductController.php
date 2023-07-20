<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Contracts\Services\AuthServiceInterface;
use App\Services\ProductService;
use Dflydev\DotAccessData\Data;

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
}
