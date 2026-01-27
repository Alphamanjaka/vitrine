<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\SaleItem;

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
                $startDate = now()->subWeeks(3)->startOfWeek(Carbon::MONDAY);
                $endDate = now()->endOfWeek(Carbon::SUNDAY);
               // Optimisation: 1 seule requête, puis groupement en PHP. Agnostique à la BDD.
                $salesDataByDay = Sale::select(
                    DB::raw('DATE(created_at) as sale_date'),
                    DB::raw('SUM(total_net) as total')
                )
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('sale_date')
                    ->pluck('total', 'sale_date');

                $salesByWeek = [];
                for ($i = 3; $i >= 0; $i--) {
                    $startOfWeek = now()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
                    $weekKey = $startOfWeek->toDateString();
                    $labels[] = "Semaine du " . $startOfWeek->format('d/m');
                    $salesByWeek[$weekKey] = 0;
                }

                foreach ($salesDataByDay as $date => $total) {
                    $weekKey = Carbon::parse($date)->startOfWeek(Carbon::MONDAY)->toDateString();
                    if (isset($salesByWeek[$weekKey])) {
                        $salesByWeek[$weekKey] += $total;
                    }
                }
                $values = array_values($salesByWeek);
                break;

            case '1year':
                // Ventes sur les 12 derniers mois
                $startDate = now()->subMonths(11)->startOfMonth();
                $endDate = now()->endOfMonth();

                $monthExpression = DB::connection()->getDriverName() === 'sqlite'
                    ? "strftime('%Y-%m', created_at)"
                    : "DATE_FORMAT(created_at, '%Y-%m')";

                // Optimisation: 1 seule requête au lieu de 12.
                $salesData = Sale::select(
                    DB::raw("$monthExpression as sale_month"),
                    DB::raw('SUM(total_net) as total')
                )
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('sale_month')
                    ->pluck('total', 'sale_month');

                for ($i = 11; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $monthKey = $date->format('Y-m');

                    $labels[] = ucfirst($date->isoFormat('MMM YYYY'));
                    $values[] = $salesData->get($monthKey, 0);
                }
                break;

            case '7days':
            default:
                // Ventes pour la semaine en cours (Lundi -> Dimanche)
                $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
                $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);

                // Optimisation: 1 seule requête au lieu de 7.
                $salesData = Sale::select(
                    DB::raw('DATE(created_at) as sale_date'),
                    DB::raw('SUM(total_net) as total')
                )
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->groupBy('sale_date')
                    ->pluck('total', 'sale_date');

                for ($i = 0; $i < 7; $i++) {
                    $day = $startOfWeek->copy()->addDays($i);
                    $dateKey = $day->toDateString();

                    $labels[] = ucfirst($day->isoFormat('dddd'));
                    $values[] = $salesData->get($dateKey, 0);
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
