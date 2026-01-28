@extends('layouts.app')

@section('title', 'Ajouter une nouvelle catégorie')
@section('content')
    <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">Ajouter une catégorie</a>
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ url('/categories') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher une catégorie..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">Toutes les catégories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filtrer</button>
                    <a href="{{ url('/categories') }}" class="btn btn-outline-secondary" rel="noopener"
                        target="_blank">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nom de la catégorie</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <th scope="row">{{ $category->id }}</th>
                            <td>{{ $category->name }}</td>
                            <td>
                                <a href="{{ route('categories.edit', $category->id) }}"
                                    class="btn btn-sm btn-primary">Modifier</a>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
