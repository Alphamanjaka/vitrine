<?php

namespace App\Services;

use App\Models\Supplier;

class SupplierService
{
    public function getAllSuppliers()
    {
        return Supplier::all();
    }

    public function createSupplier(array $data)
    {
        return Supplier::create($data);
    }
}
