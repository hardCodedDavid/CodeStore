<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Supplier;
use App\Repositories\SupplierRepository;

class SupplierService
{
    public function __construct(protected SupplierRepository $repository) 
    {
        //
    }

    public function all() : object 
    {
        // $admin = Supplier::latest()->with(['purchases'])->get();
        $admin = Supplier::latest()->get();
        
        return $admin;
    }

    public function addSupplier(array $data) : object
    {
        $supplier = $this->repository->create($data);

        return $supplier;
    }

    public function updateSupplier(Supplier $supplier, array $data) : object
    {
        $supplier = $this->repository->update($supplier, $data);

        return $supplier;
    }

    public function deleteSupplier(Supplier $supplier) : bool
    {
        return $this->repository->destroy($supplier);
    }
}
