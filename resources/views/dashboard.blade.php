@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Évolution du Chiffre d'Affaires</h5>
        <div class="btn-group">
            <a href="?period=7days" class="btn btn-sm btn-outline-primary {{ $period == '7days' ? 'active' : '' }}">7 Jours</a>
            <a href="?period=1month" class="btn btn-sm btn-outline-primary {{ $period == '1month' ? 'active' : '' }}">1 Mois</a>
            <a href="?period=1year" class="btn btn-sm btn-outline-primary {{ $period == '1year' ? 'active' : '' }}">1 An</a>
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
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Ventes (€)',
                data: {!! json_encode($values) !!},
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
