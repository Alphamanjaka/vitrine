<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SaleService
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function createSale(array $items, float $discount = 0)
    {
        return DB::transaction(function () use ($items, $discount) {
            $totalBrut = 0;
            $saleItemsData = [];

            // 1. Phase de calcul et vérification
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalBrut += $subtotal;

                $saleItemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal'   => $subtotal
                ];
            }

            // 2. Création de l'en-tête de vente
            $sale = Sale::create([
                'reference'  => 'SALE-' . now()->format('YmdHis'),
                'total_brut' => $totalBrut,
                'discount'   => $discount,
                'total_net'  => $totalBrut - $discount,
            ]);

            // 3. Création des lignes et mouvement de stock via StockService
            foreach ($saleItemsData as $data) {
                $sale->items()->create($data);

                // On délègue la sortie de stock au StockService
                $this->stockService->removeStock(
                    $data['product_id'],
                    $data['quantity'],
                    "Vente {$sale->reference}"
                );
            }

            return $sale;
        });
    }
}
