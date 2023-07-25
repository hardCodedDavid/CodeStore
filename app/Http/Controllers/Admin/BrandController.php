<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Services\BrandService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\StoreRequest;
use App\Http\Requests\Brand\UpdateRequest;

class BrandController extends Controller
{
    public function __construct(protected BrandService $service)
    {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->service->all();

        return $this->success(message: 'Data fetched successfully', data: compact('data'));
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $brand = $this->service->addBrand($request->validated());

        return $this->success(message: 'Data created successfully', data: compact('brand'));
    }

    public function update(Brand $brand, UpdateRequest $request): JsonResponse
    {
        $brand = $this->service->editBrand($brand, $request->validated());

        return $this->success(message: 'Data updated successfully', data: compact('brand'));
    }

    public function destroy(Brand $brand): JsonResponse
    {
        $deleted = $this->service->deleteBrand($brand);
        if ($deleted) {
            return $this->success('Data deleted successfully');
        } else {
            return $this->failure('An error occured!!', details: 'Can\'t delete brand, associated products found');
        }
    }
}
