<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    // 1. Liste des colonnes autorisées pour le tri (sécurité)
    $sortableColumns = ['name', 'price', 'quantity_stock', 'created_at'];

    // 2. Récupérer les paramètres ou mettre des valeurs par défaut
    $sort = in_array($request->sort, $sortableColumns) ? $request->sort : 'name';
    $order = $request->order === 'desc' ? 'desc' : 'asc';

    // On commence la requête
    $query = Product::with('category');

    // Filtre Recherche
    $query->when($request->search, function ($q, $search) {
        return $q->where('name', 'like', "%{$search}%");
    });

    // Filtre Catégorie
    $query->when($request->category, function ($q, $category_id) {
        return $q->where('category_id', $category_id);
    });
    // Appliquer le tri
    $query->orderBy($sort, $order);

    // Pagination (10 produits par page)
    // withQueryString() permet de garder les filtres quand on change de page
    $products = $query->paginate(15)->withQueryString();

    $categories = Category::all();

    return view('products.index', compact('products', 'categories'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // get categories for the select dropdown
        $categories = Category::all();
        
        return view('products.create', compact('categories'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        // if the validation fails, Laravel will automatically redirect back with errors
        // If it passes, we can safely create the product

        Product::create($request->validated()); // use mass assignment protection

        return redirect()->route('products.index')
            ->with('success', 'the product has been created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}