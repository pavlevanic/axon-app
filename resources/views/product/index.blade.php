@extends('layouts.admin')

@section('admin_content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2 text-dark"></i>Svi Proizvodi</h5>
        <a href="{{ route('product.create') }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Dodaj novi proizvod
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
                        <th>Slika</th>
                        <th>Naziv proizvoda</th>
                        <th>Kategorija</th>
                        <th>Cena</th>
                        <th>Stanje</th>
                        <th class="text-center">Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="text-muted fw-bold">#{{ $product->id }}</td>
                            <td>
                                @if($product->images->isNotEmpty())
                                    <img src="{{ asset($product->images->first()->image_path) }}" 
                                         class="rounded border shadow-sm" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <span class="text-muted small">Nema slike</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $product->name }}</div>
                                <div class="small text-muted">Slug: {{ $product->slug }}</div>
                            </td>
                            <td>
                                @if($product->category)
                                    <a href="{{ route('category.show', $product->category) }}" 
                                       class="badge bg-dark-subtle text-primary border border-dark-subtle text-decoration-underline hover-shadow">
                                        {{ $product->category->name }}
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted border">Nema kategorije</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold">{{ number_format($product->price, 2) }} €</span>
                            </td>
                            <td>
                                @if($product->stock > 0)
                                    <span class="badge bg-success-dark text-dark border border-dark-subtle">
                                        {{ $product->stock }} kom.
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        Rasprodato
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('product.edit', $product->id) }}" class="btn btn-outline-primary btn-sm" title="Izmeni">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <form action="{{ route('product.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Da li ste sigurni da želite da obrišete ovaj proizvod?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Obriši">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-cart-x display-1 text-muted"></i>
                                <p class="mt-3 text-muted">Vaš katalog je trenutno prazan.</p>
                                <a href="{{ route('product.create') }}" class="btn btn-dark btn-sm">Dodaj prvi proizvod</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection