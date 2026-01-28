@extends('layouts.app')

@section('title', 'Sélection du Profil')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="text-center mb-5">
                    <h1 class="mb-2">Bienvenue, {{ auth()->user()->name }} 👋</h1>
                    <p class="text-muted fs-5">Veuillez sélectionner votre profil de travail</p>
                </div>

                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    @if ($canAccessFrontOffice)
                        <div class="col-md-6 mb-4">
                            <a href="{{ route('sales.dashboard') }}"
                                class="card text-decoration-none h-100 shadow border-0 hover-card"
                                style="border-left: 5px solid #667eea;">
                                <div class="card-body text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-shopping-cart fa-4x text-primary"
                                            style="color: #667eea !important;"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Front Office</h5>
                                    <p class="card-text text-muted">Gestion des ventes et des commandes</p>
                                    <div class="mt-3">
                                        <span class="badge" style="background: #667eea;">Créer Ventes</span>
                                        <span class="badge" style="background: #764ba2;">Consulter Stock</span>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">Accès limité • Vendeur</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif

                    @if ($canAccessBackOffice)
                        <div class="col-md-6 mb-4">
                            <a href="{{ route('admin.dashboard') }}"
                                class="card text-decoration-none h-100 shadow border-0 hover-card"
                                style="border-left: 5px solid #dc3545;">
                                <div class="card-body text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-cogs fa-4x text-danger"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Back Office</h5>
                                    <p class="card-text text-muted">Contrôle total des produits et inventaire</p>
                                    <div class="mt-3">
                                        <span class="badge bg-danger">Produits</span>
                                        <span class="badge bg-warning text-dark">Inventaire</span>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">Accès complet • Administrateur</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                </div>

                @if (!$canAccessFrontOffice && !$canAccessBackOffice)
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Accès limité :</strong> Votre profil n'a accès à
                        aucun module. Veuillez contacter un administrateur.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .hover-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .hover-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2) !important;
        }

        .hover-card:hover .card-title {
            color: #667eea;
        }
    </style>
@endsection
