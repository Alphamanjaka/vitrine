<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\models\Product;
class PurchaseController extends Controller
{
    protected $purchaseService;
    protected $supplierService;
    public function __construct(\App\Services\PurchaseService $purchaseService, \App\Services\SupplierService $supplierService,)
    {
        $this->purchaseService = $purchaseService;
        $this->supplierService = $supplierService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // process to show all purchases
        $purchases = $this->purchaseService->getAllPurchases();
        $totalPurchases = $this->purchaseService->getTotalPurchases();
        $totalDiscounts = $this->purchaseService->getTotalDiscounts();
        return view('purchases.index',compact('purchases','totalPurchases','totalDiscounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // process to show form for creating a purchase
        $products = Product::where('quantity_stock', '>', 0)->get();
        $suppliers = $this->supplierService->getAllSuppliers();
        return view('purchases.create',compact('products', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        // process to store a new purchase
        // Validate the request data
        $data = $request->validated();

        try {
            // Process the purchase and update stock
            $purchase = $this->purchaseService->processPurchase(
                $data['supplier_id'],
                $data['products']
            );
            // Redirect with success message
            return redirect()->route('purchases.index')
                ->with('success', "L'achat {$purchase->reference} a été enregistré. Le stock a été mis à jour.");
        } catch (\Exception $e) {
            // Handle exceptions and redirect back with error message
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        $products = Product::where('quantity_stock', '>', 0)->get();
        return view('purchases.edit', compact('purchase', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $this->purchaseService->updatePurchase($request->validated());
        return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}