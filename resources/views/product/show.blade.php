@extends('layouts.app')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Početna</a></li>
            @if($product->category->slug != 'prebuilt-pc')
            <li class="breadcrumb-item"><a href="{{ route('shop.components') }}">{{$product->category->name}}</a></li>
            @else
            <li class="breadcrumb-item"><a href="{{ route('shop.prebuilts') }}">{{$product->category->name}}</a></li>
            @endif
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="main-image-container mb-3 text-center" style="overflow: hidden; cursor: crosshair;">
                    <img id="mainProductImage" 
                         src="{{ asset($product->image) }}" 
                         data-zoom="{{ asset($product->image) }}"
                         class="img-fluid rounded drift-demo-trigger" 
                         alt="{{ $product->name }}" 
                         style="max-height: 500px; object-fit: contain; transition: transform 0.3s ease;">
                </div>
        
                @if($product->images->count() > 0)
                    <div class="row g-2">
                        <div class="col-3 col-sm-2">
                            <img src="{{ asset($product->image) }}" 
                                 class="img-thumbnail thumb-img active-thumb" 
                                 style="cursor: pointer; height: 70px; width: 100%; object-fit: cover;"
                                 onclick="changeImage('{{ asset($product->image) }}', this)">
                        </div>
                        @foreach($product->images as $img)
                            @if($img->image_path !== $product->image)
                                <div class="col-3 col-sm-2">
                                    <img src="{{ asset($img->image_path) }}" 
                                         class="img-thumbnail thumb-img" 
                                         style="cursor: pointer; height: 70px; width: 100%; object-fit: cover;"
                                         onclick="changeImage('{{ asset($img->image_path) }}', this)">
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <h1 class="display-5 fw-bold text-dark">{{ $product->name }}</h1>
            <p class="text-muted mb-4">{{ $product->short_desc }}</p>

            <div class="d-flex align-items-center mb-4">
                @if($product->discount_price)
                    <h2 class="text-danger fw-bold me-3">{{ number_format($product->discount_price, 2) }} €</h2>
                    <h4 class="text-muted text-decoration-line-through">{{ number_format($product->price, 2) }} €</h4>
                @else
                    <h2 class="text-dark fw-bold">{{ number_format($product->price, 2) }} €</h2>
                @endif
            </div>

            <div class="mb-4">
                @if($product->stock > 0)
                    <span class="badge bg-dark px-4 py-2">Na stanju ({{ $product->stock }} kom)</span>
                @else
                    <span class="badge bg-dark px-4 py-2">Trenutno nedostupno</span>
                @endif
            </div>

            <hr>

            @php
    $allGroups = $product->category->attribute_groups ?? [];
    // Pronalazimo ključ za grupu "Osnovni" (case-insensitive provera)
    $groupName = isset($allGroups['Osnovni']) ? 'Osnovni' : (isset($allGroups['osnovni']) ? 'osnovni' : null);
    $osnovniKeys = $groupName ? $allGroups[$groupName] : [];
@endphp

@if(!empty($osnovniKeys) && $product->specs)
    <h5 class="fw-bold mb-3">Opšte karakteristike:</h5>
    <div class="table-responsive">
        <table class="table table-sm">
            <tbody>
                @foreach($osnovniKeys as $key)
                    @php
                        $fullKey = $groupName . '_' . $key;
                        
                        $value = $product->specs[$fullKey] ?? $product->specs[$key] ?? null;
                    @endphp

                    @if(!empty($value)) 
                        <tr>
                            <th class="py-2" style="width: 40%">{{ $key }}</th>
                            <td class="py-2">{{ $value }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endif

            @php
                 $hasDiscount = $product->discount_price > 0 && $product->discount_price < $product->price;
                 $currentPrice = $hasDiscount ? $product->discount_price : $product->price;
            @endphp

            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mt-4">
                @csrf
                @if($product->stock > 0)
                   <button type="submit" class="btn btn-dark btn-lg w-100 rounded-pill shadow-sm py-3 mb-3">
                    <i class="bi bi-cart-plus me-2"></i> Dodaj u korpu
                </button>
                @else
                    <button class="btn btn-secondary btn-lg w-100 rounded-pill py-3 mb-3" disabled>RASPRODATO</button>
                @endif
            </form>

            @auth
                <div class="alert alert-secondary mt-4 border-0 shadow-sm">
                    <h6 class="fw-bold">Admin opcije:</h6>
                    <hr>
                    <div class="d-flex gap-2">
                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-dark btn-sm">Izmeni proizvod</a>
                        <form action="{{ route('product.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Da li ste sigurni?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Obriši</button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="fw-bold border-bottom pb-2 mb-3">Opis proizvoda</h4>
            <div class="product-description lead text-secondary">
                {!! nl2br(e($product->desc)) !!}
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <h4 class="mb-4 fw-bold">Tehničke Specifikacije</h4>
    
        @php
            $detailedGroups = array_filter($allGroups, function($key) {
                return strtolower($key) !== 'osnovni';
            }, ARRAY_FILTER_USE_KEY);
            
            $count = count($detailedGroups);
        @endphp
    
        @if($count > 0)
            <div class="row">
                @php
                    $chunks = array_chunk($detailedGroups, ceil($count / 2), true);
                @endphp
    
                @foreach($chunks as $columnGroups)
                    <div class="col-md-6">
                        <div class="accordion accordion-flush">
                            @foreach($columnGroups as $groupName => $attributes)
                                <div class="accordion-item border-bottom mb-2">
                                    <h2 class="accordion-header" id="flush-heading-{{ Str::slug($groupName) }}">
                                        <button class="accordion-button collapsed fw-bold text-dark bg-transparent" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#flush-collapse-{{ Str::slug($groupName) }}" 
                                                aria-expanded="false">
                                            {{ $groupName }}
                                        </button>
                                    </h2>
                                    
                                    <div id="flush-collapse-{{ Str::slug($groupName) }}" 
                                         class="accordion-collapse collapse">
                                        <div class="accordion-body p-0">
                                            <table class="table table-sm mb-0">
                                                <tbody>
                                                    @foreach($attributes as $attr)
                                                        @php
                                                            $fullKey = $groupName . '_' . $attr;
                                                            $value = $product->specs[$fullKey] ?? $product->specs[$attr] ?? null;
                                                        @endphp
    
                                                        @if(!empty($value))
                                                            <tr>
                                                                <td class="fw-bold py-2 ps-3 w-50" style="background-color: #f8f9fa; font-size: 0.9rem;">
                                                                    {{ $attr }}
                                                                </td>
                                                                <td class="py-2 ps-3" style="font-size: 0.9rem;">
                                                                    {{ $value }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted italic">Nema dodatnih tehničkih specifikacija.</p>
        @endif
    </div>
</div>

@push('styles')
<style>
    .active-thumb { border: 2px solid #000 !important; opacity: 1 !important; }
    .thumb-img { opacity: 0.6; transition: 0.3s; }
    .thumb-img:hover { opacity: 1; }
    .main-image-container img:hover { transform: scale(1.5); }
    .main-image-container { position: relative; }
</style>
@endpush

@push('scripts')
<script>
    function changeImage(src, element) {
        const mainImg = document.getElementById('mainProductImage');
        mainImg.src = src;
        mainImg.setAttribute('data-zoom', src);
        document.querySelectorAll('.thumb-img').forEach(img => img.classList.remove('active-thumb'));
        element.classList.add('active-thumb');
    }

    const container = document.querySelector('.main-image-container');
    const img = document.getElementById('mainProductImage');
    container.addEventListener('mousemove', (e) => {
        img.style.transformOrigin = `${e.offsetX}px ${e.offsetY}px`;
    });
    container.addEventListener('mouseleave', () => {
        img.style.transformOrigin = "center";
    });
</script>
@endpush
@endsection