<?php

namespace App\Services;

use App\Models\Brand;
use App\Repositories\BrandRepository;

class BrandService
{
    public function __construct(protected BrandRepository $repository) 
    {
        //
    }

    public function all() : array 
    {
        $brands = $this->repository->getAll(page: get_page(), per_page: get_per_page());
                    
        return $brands;
    }

    public function addBrand(array $data) : object
    {
        $brand = $this->repository->create($data);

        return $brand;
    }

    public function editBrand(Brand $brand, array $data) : object
    {
        $brand = $this->repository->update($brand, $data);

        return $brand;
    }

    public function deleteBrand(Brand $brand) : bool
    {
        // Check if category has associated products
        if ($brand->products()->count() > 0) {
            return false;
        }
        
        return $this->repository->destroy($brand);
    }
}
