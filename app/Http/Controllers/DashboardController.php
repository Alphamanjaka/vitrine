<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Période par défaut : 7 jours
        $period = $request->get('period', '7days');
        $startDate = now();

        if ($period == '1month') {
            $startDate = now()->subMonth();
            $groupBy = 'DATE(created_at)';
            $format = 'd M';
        } elseif ($period == '1year') {
            $startDate = now()->subYear();
            $groupBy = 'DATE_FORMAT(created_at, "%Y-%m")';
            $format = 'M Y';
        } else {
            $startDate = now()->subDays(7);
            $groupBy = 'DATE(created_at)';
            $format = 'd M';
        }

        // Récupération des données
        $salesData = Sale::select(
            DB::raw("$groupBy as date"),
            DB::raw("SUM(total_net) as total")
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Préparation pour Chart.js
        $labels = $salesData->map(fn($s) => Carbon::parse($s->date)->translatedFormat($format));
        $values = $salesData->pluck('total');

        return view('dashboard', compact('labels', 'values', 'period'));
    }
}
