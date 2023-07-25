<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Services\BannerService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\StoreRequest;
use App\Http\Requests\Banner\UpdateRequest;

class BannerController extends Controller
{
    public function __construct(protected BannerService $service)
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
        $banner = $this->service->addBanner($request->validated());

        return $this->success(message: 'Data created successfully', data: compact('banner'));
    }

    public function update(Banner $banner, UpdateRequest $request): JsonResponse
    {
        $banner = $this->service->editBanner($banner, $request->validated());

        return $this->success(message: 'Data updated successfully', data: compact('banner'));
    }

    public function destroy(Banner $banner): JsonResponse
    {
        $this->service->deleteBanner($banner);
        
        return $this->success('Data deleted successfully');
    }
}
