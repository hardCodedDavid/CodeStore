<?php

namespace App\Repositories;

use App\Models\Supplier;
use App\Repositories\AbstractRepository;

class SupplierRepository extends AbstractRepository
{
    public function __construct(Supplier $supplier)
    {
        parent::__construct($supplier);
    }

    public function model()
    {
        return app(Supplier::class);
    }

}