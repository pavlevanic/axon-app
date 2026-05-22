@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4"><i class="bi bi-bag me-2"></i>Vaša korpa</h2>

    @if($cartItems->count() > 0)
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Proizvod</th>
                                <th>Cena</th>
                                <th style="width: 150px;">Količina</th>
                                <th>Ukupno</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
@foreach($cartItems as $item)
@php 
    // Logika za određivanje aktuelne cene (isto kao na landing page-u)
    $hasDiscount = $item->product->discount_price > 0 && $item->product->discount_price < $item->product->price;
    $currentPrice = $hasDiscount ? $item->product->discount_price : $item->product->price;
    
    // Računanje subtotala sa pravom cenom
    $subtotal = $currentPrice * $item->quantity;
    $grandTotal += $subtotal;
@endphp
<tr>
    <td>
        <div class="d-flex align-items-center">
            <img src="{{ asset($item->product->image) }}" alt="" class="rounded me-3" style="width: 60px; height: 60px; object-fit: contain; background: #f8f9fa;">
            <div>
                <div class="fw-bold text-dark">{{ $item->product->name }}</div>
            </div>
        </div>
    </td>
    <td>
        @if($hasDiscount)
            <div class="d-flex flex-column">
                <span class="text-danger text-decoration-line-through small" style="font-size: 0.8rem; ">
                    {{ number_format($item->product->price, 2) }} €
                </span>
                <span class="fw-bold ">
                    {{ number_format($item->product->discount_price, 2) }} €
                </span>
            </div>
        @else
            <span class="fw-bold text-dark">{{ number_format($item->product->price, 2) }} €</span>
        @endif
    </td>
    <td>
        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center">
            @csrf
            @method('PATCH')
            <input type="number" name="quantity" value="{{ $item->quantity }}" class="form-control form-control-sm text-center" min="1" onchange="this.form.submit()" style="width: 70px;">
        </form>
    </td>
    <td class="fw-bold text-dark">
        {{ number_format($subtotal, 2) }} €
    </td>
    <td class="text-end">
        <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-link text-danger p-0" title="Ukloni">
                <i class="bi bi-trash fs-5"></i>
            </button>
        </form>
    </td>
</tr>
@endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i>Nastavi kupovinu
            </a>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Pregled narudžbine</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ukupno artikala:</span>
                        <span>{{ $cartItems->sum('quantity') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 fw-bold">Za uplatu:</span>
                        <span class="h5 fw-bold text-primary">{{ number_format($grandTotal, 2) }} €</span>
                    </div>
                    
                    <button class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm py-3">
                        Idi na plaćanje <i class="bi bi-credit-card ms-2"></i>
                    </button>
                </div>
            </div>
            
            <div class="alert alert-light mt-3 border-0 small shadow-sm text-center">
                <i class="bi bi-shield-check text-success me-1"></i> Sigurna kupovina na AXON platformi
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5 shadow-sm bg-white rounded">
        <i class="bi bi-bag-x display-1 text-muted"></i>
        <h4 class="mt-4">Vaša korpa je trenutno prazna</h4>
        <p class="text-muted">Dodajte neke proizvode u korpu da biste ih videli ovde.</p>
        <a href="{{ route('shop.components') }}" class="btn btn-primary btn-lg rounded-pill px-5 mt-3 shadow">Započni kupovinu</a>
    </div>
    @endif
</div>
@endsection