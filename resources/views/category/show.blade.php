@extends('layouts.admin')

@section('admin_content')
<div class="mb-4">
    <a href="{{ route('category.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Nazad na sve kategorije
    </a>
</div>

<div class="row g-4">
    {{-- Detalji Kategorije --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark">Informacije o kategoriji</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Naziv:</label>
                    <p class="fs-5 fw-bold text-dark mb-0">{{ $category->name }}</p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Slug:</label>
                    <p class="text-secondary mb-0">{{ $category->slug }}</p>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Opis:</label>
                    <p class="text-secondary small">{{ $category->description ?? 'Nema opisa za ovu kategoriju.' }}</p>
                </div>
                <hr class="text-muted opacity-25">
                <div class="d-grid gap-2">
                    <a href="{{ route('category.edit', $category) }}" class="btn btn-dark">
                        <i class="bi bi-pencil me-2"></i> Izmeni kategoriju
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista proizvoda u ovoj kategoriji --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Proizvodi u kategoriji ({{ $category->products->count() }})</h5>
                <a href="{{ route('product.create', ['category_id' => $category->id]) }}" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-plus-lg"></i> Dodaj u ovu kategoriju
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Slika</th>
                                <th>Proizvod</th>
                                <th>Cena</th>
                                <th class="text-center">Akcije</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($category->products as $product)
                                <tr>
                                    <td>
                                        @if($product->images->isNotEmpty())
                                            <img src="{{ asset($product->images->first()->image_path) }}" 
                                                 class="rounded border shadow-sm" 
                                                 style="width: 40px; height: 40px; object-fit: cover;"
                                                 alt="{{ $product->name }}">
                                        @else
                                            <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted small" style="width: 40px; height: 40px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                                        <div class="small text-muted">Stock: {{ $product->stock }} kom.</div>
                                    </td>
                                    <td><span class="fw-bold">{{ number_format($product->price, 2) }} €</span></td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('product.edit', $product->id) }}" class="btn btn-outline-dark btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('product.show', $product->id) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">
                                        Nema proizvoda dodeljenih ovoj kategoriji.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection