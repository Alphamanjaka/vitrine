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
            $totalAmount = 0;

            // 1. Créer l'achat
            $purchase = Purchase::create([
                'reference' => 'PUR-' . now()->format('YmdHis'),
                'supplier_id' => $supplierId,
                'total_cost' => 0, // On mettra à jour après
                'total_amount' => 0, // Initialisation
                'total_net' => 0,
                'discount' => 0
            ]);

            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['unit_cost'];
                $totalCost += $subtotal;
                $subtotal = $item['quantity'] * $item['unit_price'];
                $totalAmount += $subtotal;

                // 2. Créer la ligne d'achat
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);

                // 3. AUGMENTER le stock via StockService
                $this->stockService->addStock(
                    $item['product_id'],
                    $item['quantity'],
                    "Achat fournisseur {$purchase->reference}"
                );
            }

            $purchase->update(['total_cost' => $totalCost]);
            $purchase->update([
                'total_amount' => $totalAmount,
                'total_net' => $totalAmount // Pas de remise gérée ici pour l'instant
            ]);
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
            'totalCost' => Purchase::sum('total_net'),
            'totalPurchases' => Purchase::count(),
        ];
    }
    public function     updatePurchase($data)

    public function updatePurchase($data)
    {
        $purchase = Purchase::findOrFail($data['id']);
        $purchase->update($data);
        return $purchase;
    }
}