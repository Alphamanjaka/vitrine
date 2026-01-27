<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{

    public function create($data)
    {
        // Logic to create a category
        $category = Category::create($data);
        return $category;

    }
    public function getAllCategory()
    {
        return Category::all();
    }
}
