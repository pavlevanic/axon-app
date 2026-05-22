@extends('layouts.admin') 

@section('admin_content') 
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="bi bi-tags me-2"></i>Upravljanje Kategorijama</h5>
        <a href="{{ route('category.create') }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Dodaj novu
        </a>
    </div>

    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 80px;">Slika</th> 
                        <th>Ime kategorije</th>
                        <th>Opis</th>
                        <th>Autor</th>
                        <th class="text-center">Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td class="text-muted fw-bold">#{{ $category->id }}</td>
                            <td>
                                @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" 
                                alt="{{ $category->name }}" 
                                style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted" 
                                         style="width: 50px; height: 50px;">
                                        <i class="bi bi-image small"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('category.show', ['category' => $category->slug]) }}"class="text-decoration-none text-black fw-semibold">
                                    {{ $category->name }}
                                </a>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ Str::limit($category->desc, 50) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-person me-1"></i> {{ $category->author->name ?? 'Nepoznato' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('category.edit', $category) }}" class="btn btn-outline-primary btn-sm" title="Izmeni">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <form action="{{ route('category.destroy', $category) }}" method="POST" onsubmit="return confirm('Da li ste sigurni da želite da obrišete ovu kategoriju?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Obriši">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($categories->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-folder2-open display-1 text-muted"></i>
                <p class="mt-3 text-muted">Trenutno nema kreiranih kategorija.</p>
            </div>
        @endif
    </div>
</div>
@endsection