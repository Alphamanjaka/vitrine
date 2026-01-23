<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Exception;

class StockService
{
    public function removeStock(int $productId, int $quantity, string $reason)
    {
        $product = Product::findOrFail($productId);

        // check if there is enough stock
        if ($product->quantity_stock < $quantity) {
            throw new Exception("Stock insuffisant pour : {$product->name}");
        }
        // reduce   the stock
        $product->decrement('quantity_stock', $quantity);
        // check for alert stock
        if ($product->quantity_stock <= $product->alert_stock) {
            // Here you could trigger a notification, email, etc.
            $admin= User::first(); // récupérer l'administrateur
            $admin->notify(new LowStockAlert($product));
        }

        // log the stock movement
        return StockMovement::create([
            'product_id' => $productId,
            'type' => 'out',
            'quantity' => $quantity,
            'reason' => $reason,
        ]);
    }

    public function addStock(int $productId, int $quantity, string $reason)
    {
        $product = Product::findOrFail($productId);
        $product->increment('quantity_stock', $quantity);

        return StockMovement::create([
            'product_id' => $productId,
            'type' => 'in',
            'quantity' => $quantity,
            'reason' => $reason,
        ]);
    }
}
