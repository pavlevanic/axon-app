@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Rezultati pretrage za: "<span class="text-secondary">{{ $query }}</span>"</h2>

    @if($products->count() > 0)
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach($products as $product)
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-0">
                        <img src="{{ asset($product->image) }}" class="card-img-top p-3" alt="{{ $product->name }}" style="height: 200px; object-fit: contain;">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold text-dark">{{ $product->name }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($product->short_desc, 60) }}</p>
                            <p class="fw-bold fs-5">{{ number_format($product->price, 2) }} €</p>
                            <a href="{{ route('product.show', $product->slug) }}" class="btn btn-outline-dark btn-sm rounded-0 w-100">Pogledaj detalje</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $products->appends(['query' => $query])->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
            <h3 class="mt-3">Nismo pronašli ništa što odgovara vašoj pretrazi.</h3>
            <p class="text-secondary">Pokušajte sa nekim drugim terminom (npr. RTX,Ryzen...)</p>
            <a href="{{ url('/') }}" class="btn btn-dark rounded-0 mt-3 px-4">Povratak na početnu</a>
        </div>
    @endif
</div>
@endsection