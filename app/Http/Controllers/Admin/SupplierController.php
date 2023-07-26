<?php

namespace App\Http\Controllers\Admin;


use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreRequest;
use App\Http\Requests\Supplier\UpdateRequest;

class SupplierController extends Controller
{
    public function __construct(protected SupplierService $service)
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
        $supplier = $this->service->addSupplier($request->validated());

        return $this->success(message: 'Data created successfully', data: compact('supplier'));
    }

    public function update(Supplier $supplier, UpdateRequest $request): JsonResponse
    {
        $supplier = $this->service->updateSupplier($supplier, $request->validated());

        return $this->success(message: 'Data updated successfully', data: compact('supplier'));
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $this->service->deleteSupplier($supplier);
        
        return $this->success('Data deleted successfully');
    }
}
