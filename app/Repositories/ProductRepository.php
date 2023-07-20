<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\AbstractRepository;

class ProductRepository extends AbstractRepository
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function model()
    {
        return app(Product::class);
    }

}