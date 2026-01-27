<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 100 products
        \App\Models\Product::factory(20)->create();

        // Create a specific user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Supplier::create([
            'name' => 'Grossiste Informatique SARL',
            'email' => 'contact@grossiste-info.test',
            'phone' => '034 11 222 33',
            'address' => 'Rue des Tech, Antananarivo'
        ]);

        Supplier::create([
            'name' => 'Buro-Top Fournisseurs',
            'email' => 'sales@burotop.test',
            'phone' => '032 55 444 11',
            'address' => 'Avenue de l\'Indépendance'
        ]);

        Supplier::create([
            'name' => 'Import Global Madagascar',
            'email' => 'import@global.test',
            'phone' => '020 22 555 99',
            'address' => 'Zone Industrielle Akorondrano'
        ]);

        // --- SEEDING DES VENTES (SALES) ---
        // On crée 30 ventes
        \App\Models\Sale::factory(30)->create()->each(function ($sale) {
            // Pour chaque vente, on prend entre 1 et 5 produits au hasard
            $products = \App\Models\Product::inRandomOrder()->take(rand(1, 5))->get();
            $totalBrut = 0;

            foreach ($products as $product) {
                $qty = rand(1, 10);
                $price = $product->price;
                $subtotal = $price * $qty;

                // Création de la ligne de vente
                \App\Models\SaleItem::factory()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $totalBrut += $subtotal;
            }

            // Mise à jour des totaux de la vente
            $discount = rand(0, 1) ? rand(5, 50) : 0; // Une chance sur deux d'avoir une remise
            $sale->update([
                'total_brut' => $totalBrut,
                'discount' => $discount,
                'total_net' => max(0, $totalBrut - $discount),
            ]);
        });

        // --- SEEDING DES ACHATS (PURCHASES) ---
        // On crée 15 achats liés aux fournisseurs existants
        \App\Models\Purchase::factory(15)->create([
            'supplier_id' => fn() => Supplier::inRandomOrder()->first()->id
        ]);
    }
}
