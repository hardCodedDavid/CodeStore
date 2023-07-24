<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\AbstractRepository;

class CategoryRepository extends AbstractRepository
{
    public function __construct(Category $category)
    {
        parent::__construct($category);
    }

    public function model()
    {
        return app(Category::class);
    }
}
