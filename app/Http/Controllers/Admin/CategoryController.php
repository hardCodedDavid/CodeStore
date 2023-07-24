<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service)
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
        $catrgory = $this->service->addCategory($request->validated());

        return $this->success(message: 'Data created successfully', data: compact('catrgory'));
    }

    public function update(Category $category, UpdateRequest $request): JsonResponse
    {
        $data = $this->service->editCategory($category, $request->validated());

        return $this->success('Data updated successfully', data: compact('category'));
    }

    public function destroy(Category $category): JsonResponse
    {
        // $this->service->deleteCategory($category);

        // return $this->success('Data deleted successfully');

        $deleted = $this->service->deleteCategory($category);
        if ($deleted) {
            return $this->success('Data deleted successfully'); 
        } else {
            return $this->failure('An error occured!!', details: 'Can\'t delete category, associated products found');
        }
    }
}
