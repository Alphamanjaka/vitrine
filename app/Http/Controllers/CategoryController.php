<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $categories = $this->categoryService->getAllCategory();
        return view("categories.index", compact("categories"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = $this->categoryService->getAllCategory();
        return view("categories.create", compact("categories"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        // The validation is handled by StoreCategoryRequest.
        // If it fails, Laravel automatically redirects back with errors.
        try {
            // We only pass validated data to the service for security.
            $this->categoryService->create($request->validated());

            return redirect()->route("categories.index")->with("success", "Category created successfully.");
        } catch (\Exception $e) {
            // \Log::error($e->getMessage()); // It's good practice to log the actual error for debugging.
            return redirect()->back()->with("error", "An unexpected error occurred. Please try again.")->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // detail of category
        $category = Category::findOrFail($id);
        return view("categories.show", compact("category"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}