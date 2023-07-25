<?php

namespace App\Http\Controllers\Admin;

use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\VariationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Variation\StoreRequest;
use App\Http\Requests\Variation\UpdateRequest;

class VariationController extends Controller
{
    public function __construct(protected VariationService $service)
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
        $catrgory = $this->service->addVariation($request->validated());

        return $this->success(message: 'Data created successfully', data: compact('catrgory'));
    }

    public function update(Variation $variation, UpdateRequest $request): JsonResponse
    {
        $catrgory = $this->service->editVariation($variation, $request->validated());

        return $this->success(message: 'Data updated successfully', data: compact('catrgory'));
    }

    public function destroy(Variation $variation): JsonResponse
    {
        $deleted = $this->service->deleteVariation($variation);
        if ($deleted) {
            return $this->success('Data deleted successfully');
        } else {
            return $this->failure('An error occured!!', details: 'Can\'t delete variation, associated products found');
        }
    }
}
