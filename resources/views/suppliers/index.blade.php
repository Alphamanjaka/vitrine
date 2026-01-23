@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary mb-3">+ Ajouter</a>
    <div class="container">
        <h1>Liste des Fournisseurs</h1>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Adresse</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->id }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_email }}</td>
                        <td>{{ $supplier->phone_number }}</td>
                        <td>{{ $supplier->address }}</td>
                        <td>
                            <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-info btn-sm">Voir</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
