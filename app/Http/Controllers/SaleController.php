<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use App\Http\Requests\StoreSaleRequest;

class SaleController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create()
    {
        $products = Product::where('quantity_stock', '>', 0)->get();
        return view('sales.create', compact('products'));
    }

    public function store(StoreSaleRequest $request)
    {
        try {
            // On récupère les données validées par ta Request
            $validated = $request->validated();

            // Appel au service pour créer la vente, les lignes et sortir le stock
            $sale = $this->saleService->createSale(
                $validated['products'],
                $validated['discount'] ?? 0
            );

            return redirect()
                ->route('sales.show', $sale->id) // On redirige vers le détail de la vente
                ->with('success', "Vente {$sale->reference} validée avec succès !");
        } catch (\Exception $e) {
            // En cas de stock insuffisant ou autre erreur, on revient en arrière
            return back()
                ->withInput() // Garde les saisies du formulaire
                ->with('error', "Erreur lors de la vente : " . $e->getMessage());
        }
    }
    public function index()
    {
        // On récupère les ventes triées par la plus récente
        $sales = \App\Models\Sale::latest()->paginate(15);

        // Calculs rapides pour un petit tableau de bord en haut de page
        $totalRevenue = \App\Models\Sale::sum('total_net');
        $totalDiscounts = \App\Models\Sale::sum('discount');

        return view('sales.index', compact('sales', 'totalRevenue', 'totalDiscounts'));
    }

    public function show($id)
    {
        // On charge la vente avec ses items ET les produits liés aux items
        $sale = \App\Models\Sale::with('items.product')->findOrFail($id);
        return view('sales.show', compact('sale'));
    }

    public function exportPdf($id)
    {
        $sale = Sale::with('items.product')->findOrFail($id);

        // On utilise une vue spécifique simplifiée pour le PDF (sans le layout Bootstrap complexe)
        $pdf = Pdf::loadView('sales.pdf', compact('sale'));

        return $pdf->download("facture_{$sale->reference}.pdf");
    }
}
