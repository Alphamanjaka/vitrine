<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;

class ProductService
{
    /**
     * Get all products with filtering, sorting and pagination
     */
    public function getAllProducts($filters = [])
    {
        $sortableColumns = ['name', 'price', 'quantity_stock', 'created_at'];
        $sort = in_array($filters['sort'] ?? 'name', $sortableColumns) ? $filters['sort'] : 'name';
        $order = ($filters['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = $filters['per_page'] ?? 15;

        $query = Product::with('category');

        // Filter by search
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        // Filter by category
        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        // Apply sorting and pagination
        return $query->orderBy($sort, $order)->paginate($perPage)->appends(request()->query());
    }

    /**
     * Get all categories (for dropdown)
     */
    public function getAllCategories()
    {
        return Category::all();
    }

    /**
     * Get products with available stock
     */
    public function getAvailableProducts()
    {
        return Product::where('quantity_stock', '>', 0)->get();
    }

    /**
     * Get single product by ID
     */
    public function getProductById($id)
    {
        return Product::with('category')->findOrFail($id);
    }

    /**
     * Create a new product
     */
    public function createProduct($data)
    {
        return Product::create($data);
    }

    /**
     * Update product
     */
    public function updateProduct($id, $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    /**
     * Delete product
     */
    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return $product;
    }

    /**
     * Get products with low stock
     */
    public function getLowStockProducts()
    {
        return Product::whereRaw('quantity_stock < alert_stock')->get();
    }

    /**
     * Check if product has available stock
     */
    public function hasAvailableStock($productId, $quantity = 1)
    {
        $product = Product::findOrFail($productId);
        return $product->quantity_stock >= $quantity;
    }
}
