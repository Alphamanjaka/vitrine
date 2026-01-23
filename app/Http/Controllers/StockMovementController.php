<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    protected $stockService;
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stockMovements = StockMovement::with('product')->orderBy('created_at', 'desc')->get();
        return view('stock_movements.index', compact('stockMovements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // data validation
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'type' => 'required|in:addition,removal',
            'reason' => 'nullable|string',
        ]);
        // using transaction to ensure data integrity
        DB::transaction(function () use ($validatedData) {
            // Create stock movement
            StockMovement::create($validatedData);
            // Update product stock quantity
            $product = Product::findOrFail($validatedData['product_id']);

            // Adjust stock based on movement type
            if ($validatedData['type'] === 'in') {
                $product->quantity_stock += $validatedData['quantity'];
            } else {
                // Ensure stock doesn't go negative
                if($product->quantity_stock < $validatedData['quantity']){
                    throw new \Exception('Insufficient stock for this removal.');
                }

                $product->quantity_stock -= $validatedData['quantity'];
            }
            $product->save();
        });
        return redirect()->back()->with('success','Stock movement created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockMovement $stockMovement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockMovement $stockMovement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement)
    {
        //
    }
}