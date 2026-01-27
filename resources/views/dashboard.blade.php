@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Ventes Aujourd'hui</h6>
                    <p class="card-text fs-4 fw-bold">{{ number_format($salesToday, 2, ',', ' ') }} €</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Ventes ce Mois-ci</h6>
                    <p class="card-text fs-4 fw-bold">{{ number_format($salesThisMonth, 2, ',', ' ') }} €</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Produit Favori</h6>
                    @if ($mostSoldProduct && $mostSoldProduct->product)
                        <p class="card-text fs-5 fw-bold" title="{{ $mostSoldProduct->product->name }}">
                            {{ \Illuminate\Support\Str::limit($mostSoldProduct->product->name, 20) }}</p>
                        <small>Vendu {{ $mostSoldProduct->total_quantity }} fois</small>
                    @else
                        <p class="card-text fs-5 fw-bold">N/A</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Produit Moins Vendu</h6>
                    @if ($leastSoldProduct && $leastSoldProduct->product)
                        <p class="card-text fs-5 fw-bold" title="{{ $leastSoldProduct->product->name }}">
                            {{ \Illuminate\Support\Str::limit($leastSoldProduct->product->name, 20) }}</p>
                        <small>Vendu {{ $leastSoldProduct->total_quantity }} fois</small>
                    @else
                        <p class="card-text fs-5 fw-bold">N/A</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Évolution du Chiffre d'Affaires</h5>
            <div class="btn-group">
                <a href="?period=7days"
                    class="btn btn-sm btn-outline-primary {{ $period == '7days' ? 'active' : '' }}">Cette semaine</a>
                <a href="?period=1month" class="btn btn-sm btn-outline-primary {{ $period == '1month' ? 'active' : '' }}">4
                    semaines</a>
                <a href="?period=1year" class="btn btn-sm btn-outline-primary {{ $period == '1year' ? 'active' : '' }}">1
                    An</a>
                <a href="?period=by_product"
                    class="btn btn-sm btn-outline-primary {{ $period == 'by_product' ? 'active' : '' }}">Par produit</a>
            </div>
        </div>
        <div class="card-body">
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            type: {!! json_encode($chartType ?? 'line') !!},
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Ventes (€)',
                    label: {!! json_encode($chartLabel ?? 'Ventes (€)') !!},
                    data: {!! json_encode($values) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    backgroundColor: {!! json_encode($chartType ?? 'line') !!} === 'bar' ?
                        'rgba(13, 110, 253, 0.5)' : 'rgba(13, 110, 253, 0.1)',
                    fill: {!! json_encode($chartType ?? 'line') !!} === 'line',
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
