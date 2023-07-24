<?php

namespace App\Services;

use App\Models\Category;
use App\Exceptions\CustomException;
use Intervention\Image\Facades\Image;
use Spatie\QueryBuilder\QueryBuilder;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(protected CategoryRepository $repository) 
    {
        //
    }

    public function all(string $order = 'name', string $dir = 'asc'): Collection|array
    {
        return QueryBuilder::for(Category::class)->allowedIncludes(['subcategories', 'banners'])->get();
    }

    public function addCategory(array $data): object
    {
        $category = $this->repository->create($data);

        // Create subcategories if exists
        if (request('subcategories')) {
            foreach (explode(',', request('subcategories')) as $subCategory) {
                $category->subCategories()->create(['name' => $subCategory]);
            }
        }
        //Create banners
        foreach (request('banners') as $banner) {
            $destination = 'banners';
            $transferFile = 'BN'.time().mt_rand(100, 999).'.'.$banner->getClientOriginalExtension();
            $location = $banner->move($destination, $transferFile);
            $category->banners()->create(['url' => $location]);
        }

        return $this->repository->find($category['id']);
    }

    public function editCategory(Category $category, array $data): object
    {
        $category = $this->repository->update($category, $data);

        // Update subcategories if exists
        if (request('subcategories')) {
            $updatedSubCategories = [];
            foreach (request('subcategories') as $subCategoryData) {
                if (isset($subCategoryData['id'])) {
                    // Update existing subcategory
                    $subcategory = $category->subCategories()->find($subCategoryData['id']);
                    if ($subcategory) {
                        $subcategory->update(['name' => $subCategoryData['name']]);
                        $updatedSubCategories[] = $subcategory;
                    }
                } else {
                    // Create new subcategory
                    $newSubcategory = $category->subCategories()->create(['name' => $subCategoryData]);
                    $updatedSubCategories[] = $newSubcategory;
                }
            }
            // Remove subcategories that were not updated or added
            $category->subCategories()->whereNotIn('id', array_map(function ($subCategory) {
                return $subCategory['id'];
            }, $updatedSubCategories))->delete();
        }

        foreach (request('banners') as $banner) {
            $category->banners()->delete();

            $destination = 'banners';
            $transferFile = 'BN'.time().mt_rand(100, 999).'.'.$banner->getClientOriginalExtension();
            $location = $banner->move($destination, $transferFile);
            $category->banners()->create(['url' => $location]);
        }

        return $this->repository->find($category['id']);
    }

    public function deleteCategory(Category $category): bool
    {
        // Check if category has associated products
        if ($category->products()->count() > 0) {
            return false;
        }

        // Delete category and associated subcategories
        $category->subCategories()->delete();
        
        return $this->repository->destroy($category);
    }
}
