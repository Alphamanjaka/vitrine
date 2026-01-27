<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // --- Indicateurs Clés (KPIs) ---
        $salesToday = Sale::whereDate('created_at', today())->sum('total_net');
        $salesThisMonth = Sale::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_net');

        $soldProductQuery = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_quantity')
        )
            ->groupBy('product_id')
            ->with('product:id,name'); // Eager load pour l'efficacité

        $mostSoldProduct = (clone $soldProductQuery)->orderBy('total_quantity', 'desc')->first();
        $leastSoldProduct = (clone $soldProductQuery)->orderBy('total_quantity', 'asc')->first();

        // --- Données pour le graphique ---
        $period = $request->get('period', '7days'); // Période par défaut
        $labels = [];
        $values = [];
        Carbon::setLocale('fr'); // Pour avoir les noms des jours/mois en français

        switch ($period) {
            case '1month':
                // Ventes sur les 4 dernières semaines
                for ($i = 3; $i >= 0; $i--) {
                    $startOfWeek = now()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
                    $endOfWeek = now()->subWeeks($i)->endOfWeek(Carbon::SUNDAY);
                    $sales = Sale::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('total_net');

                    $labels[] = "Semaine du " . $startOfWeek->format('d/m');
                    $values[] = $sales;
                }
                break;

            case '1year':
                // Ventes sur les 12 derniers mois
                for ($i = 11; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $labels[] = ucfirst($date->isoFormat('MMM YYYY'));
                    $values[] = Sale::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->sum('total_net');
                }
                break;

            case '7days':
            default:
                // Ventes pour la semaine en cours (Lundi -> Dimanche)
                $period = '7days'; // Assurer la valeur pour la vue
                $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
                for ($i = 0; $i < 7; $i++) {
                    $day = $startOfWeek->copy()->addDays($i);
                    $labels[] = ucfirst($day->isoFormat('dddd'));
                    $values[] = Sale::whereDate('created_at', $day)->sum('total_net');
                }
                break;
        }

        return view('dashboard', compact(
            'salesToday',
            'salesThisMonth',
            'mostSoldProduct',
            'leastSoldProduct',
            'period',
            'labels',
            'values'
        ));
    }
}
