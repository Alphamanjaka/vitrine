<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('stock_movements', function (Blueprint $table) {
        $table->id();

        // Relation avec le produit
        $table->foreignId('product_id')->constrained()->onDelete('cascade');

        // Quantité du mouvement (toujours positive)
        $table->integer('quantity');

        // Type de mouvement : 'in' (entrée) ou 'out' (sortie)
        $table->enum('type', ['in', 'out']);

        // Pour savoir pourquoi le stock a bougé (ex: "Vente", "Réapprovisionnement", "Casse")
        $table->string('reason')->nullable();

        // Date et heure du mouvement
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};