<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Repositories\AbstractRepository;

class BrandRepository extends AbstractRepository
{
    public function __construct(Brand $brand)
    {
        parent::__construct($brand);
    }

    public function model()
    {
        return app(Brand::class);
    }
}
