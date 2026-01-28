<?php

namespace App\Services;

use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class PurchaseService
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function processPurchase(int $supplierId, array $items)
    {
        return DB::transaction(function () use ($supplierId, $items) {
            $totalCost = 0;

            // 1. Créer l'achat
            $purchase = Purchase::create([
                'reference' => 'PUR-' . now()->format('YmdHis'),
                'supplier_id' => $supplierId,
                'total_cost' => 0 // On mettra à jour après
            ]);

            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['unit_cost'];
                $totalCost += $subtotal;

                // 2. Créer la ligne d'achat
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                ]);

                // 3. AUGMENTER le stock via StockService
                $this->stockService->addStock(
                    $item['product_id'],
                    $item['quantity'],
                    "Achat fournisseur {$purchase->reference}"
                );
            }

            $purchase->update(['total_cost' => $totalCost]);
            return $purchase;
        });
    }

    /**
     * Get all purchases with pagination
     */
    public function getAllPurchases($perPage = 15)
    {
        return Purchase::latest()->paginate($perPage);
    }

    /**
     * Get single purchase by ID
     */
    public function getPurchaseById($id)
    {
        return Purchase::with('items.product', 'supplier')->findOrFail($id);
    }

    /**
     * Get purchase statistics
     */
    public function getPurchaseStatistics()
    {
        return [
            'totalCost' => Purchase::sum('total_cost'),
            'totalPurchases' => Purchase::count(),
        ];
    }
    public function     updatePurchase($data)
    {
        $purchase = Purchase::findOrFail($data['id']);
        $purchase->update($data);
        return $purchase;
    }
}
