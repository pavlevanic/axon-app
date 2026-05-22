@extends('layouts.app')

@section('content')
    <div>
        <div id="axonHeroCarousel" class="carousel slide carousel-fade p-0 m-0" data-bs-ride="carousel">

            <div class="carousel-inner mt-4">
                @if($heroNews->count() > 0)
                    @foreach($heroNews as $key => $news)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}" data-bs-interval="5000">
                            <div style="
                                background: url('{{ asset($news->image) }}') center/cover no-repeat;
                                height: 63.5vh;
                                width: 90%;
                                display: flex;
                                margin-left: auto;
                                margin-right: auto;
                                border-radius: 15px;
                            " class="d-flex align-items-center">
        
                                <div class="container p-5">
                                    <div class="row">
                                        <div class="col-lg-8 {{ $news->dark_image ? 'text-white' : 'text-dark' }}">
                                            <h1 class="display-2 fw-bold mb-3">{{ $news->title }}</h1>
                                            <p class="lead fs-4 mb-4" style="max-width: 600px; opacity: 0.9;">
                                                {{ $news->summary }}
                                            </p>
                                            <a href="{{ route('news.show', $news->slug) }}" 
                                               class="btn {{ $news->dark_image ? 'btn-light text-black' : 'btn-primary text-white' }} btn-lg px-5 py-3 rounded-4 fw-bold shadow">
                                                PROČITAJ VIŠE
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        
            <button class="carousel-control-prev hero-arrow" type="button" data-bs-target="#axonHeroCarousel" data-bs-slide="prev">
                <i class="bi bi-chevron-left text-white fs-4"></i>
            </button>
            <button class="carousel-control-next hero-arrow" type="button" data-bs-target="#axonHeroCarousel" data-bs-slide="next">
                <i class="bi bi-chevron-right text-white fs-4"></i>
            </button>
        </div>

        <div class="container py-5 ">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h6 class="text-primary fw-bold text-uppercase mb-1">Premium Ponuda</h6>
                    <h2 class="fw-bold display-5">Izdvajamo za vas</h2>
                </div>
                <a href="{{route('shop.prebuilts')}}" class="btn btn-outline-primary rounded-0 px-4 fw-bold">POGLEDAJ SVE</a>
            </div>
        
            <div class="row g-4">
              @foreach($products as $product)
              @php
                  $hasDiscount = $product->discount_price > 0 && $product->discount_price < $product->price;
                  if ($hasDiscount) {
                    $percentage = round(100 - ($product->discount_price / $product->price * 100));
                    }
                    $isOutOfStock = $product->stock <= 0;
              @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    @php $isOutOfStock = $product->stock <= 0; @endphp
                    
                    <div class="card h-100 border-0 shadow-sm product-card transition ">
                        
                        
                        <div class="position-absolute top-0 start-0 m-3" style="z-index: 2;">
                            @if($isOutOfStock)
                                <span class="badge bg-primary rounded-0 px-3 py-2 fw-bold shadow-sm" style="opacity: 70%">
                                    RASPRODATO
                                </span>
                            @elseif($hasDiscount)
                                <span class="badge bg-primary rounded-0 px-3 py-2 fw-bold shadow-sm">
                                    -{{ $percentage }}%
                                </span>
                            @else
                                <span class="badge bg-primary rounded-0 px-3 py-2 fw-bold shadow-sm">
                                    NOVO
                                </span>
                            @endif
                        </div>
                
                        <div class="p-3 text-center bg-light position-relative">
                            <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ asset($product->image) }}" 
                                 class="img-fluid {{ $isOutOfStock ? 'grayscale-filter' : '' }}" 
                                 style="height: 200px; object-fit: contain;" 
                                 alt="{{ $product->name }}">
                            </a>
                        </div>
                
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted text-uppercase mb-1">{{ $product->category->name ?? 'Komponente' }}</small>
                            <h5 class="card-title fw-bold mb-3">{{ $product->name }}</h5>
                            
                            <div class="mt-auto">
                                <div class="d-flex flex-column">
                                    @if($isOutOfStock)
                                        <span class="fs-4 fw-bold text-muted">Trenutno nedostupno</span>
                                    @elseif($hasDiscount)
                                        <span class="text-muted text-decoration-line-through small">
                                            {{ number_format($product->price, 2, ',', '.') }} €
                                        </span>
                                        <span class="fs-4 fw-bold text-danger">
                                            {{ number_format($product->discount_price, 2, ',', '.') }} €
                                        </span>
                                    @else
                                        <span class="fs-4 fw-bold text-dark">
                                            {{ number_format($product->price, 2, ',', '.') }} €
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Akcije --}}
                                <div class="d-grid gap-2 mt-3">
                                    <a href="{{ route('product.show', $product->slug) }}" class="btn btn-primary rounded-0 fw-bold">DETALJI</a>
                                    
                                    
                                    @if(!$isOutOfStock)
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary rounded-0 w-100 fw-bold">
                                                <i class="bi bi-cart3"></i> U KORPU
                                            </button>
                                        </form>
                                    @else
                                        
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <style>
                    .grayscale-filter {
                        filter: grayscale(100%);
                        opacity: 0.5;
                    }
                    .product-card {
                        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
                    }
                    .product-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
                    }
                </style>
              @endforeach
            </div>
        </div>

        <section class="py-5 bg-light">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Istražite Kategorije</h2>
                </div>
        
                <div class="row g-4">
                    @foreach($categories as $category)
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('shop.components', ['category' => $category->slug]) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm hover-lift">
                                <div class="position-relative overflow-hidden">
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}"
                                             class="card-img-top img-fluid"
                                             alt="{{ $category->name }}"
                                             style="height: 200px; object-fit: contain;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="height: 200px;">
                                            <i class="bi bi-image display-4"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body text-center py-3">
                                    <h5 class="card-title mb-1 text-dark fw-bold">{{ $category->name }}</h5>
                                    @if($category->desc)
                                    <p class="card-text text-muted small mb-0">
                                        {{ Str::limit(strip_tags($category->desc), 40) }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-5 bg-white">
            <div class="container py-4">
                <div class="row g-4">
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <div>
                            <h6 class="text-primary  fw-bold text-uppercase mb-1">naša obećanja</h6>
                            <h2 class="fw-bold display-5">Zasto da izaberete bas nas?</h2>
                        </div>
                        
                    </div>
                    
                    <!-- Performanse -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm feature-card p-3">
                            <div class="card-body">
                                <div class="feature-icon-wrapper mb-4">
                                    <i class="bi bi-speedometer2 text-primary fs-3"></i>
                                </div>
                                <h5 class="fw-bold text-uppercase mb-3" style="letter-spacing: 1px; font-size: 1rem;">Performanse na prvom mestu</h5>
                                <p class="text-secondary small">Svaki deo i PC je dizajniran za optimalan protok vazduha i stabilnost — čuvajući tvoj sistem hladnim, tihim i pouzdanim.</p>
                            </div>
                        </div>
                    </div>
        
                    <!-- Univerzalni dizajn -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm feature-card p-3">
                            <div class="card-body">
                                <div class="feature-icon-wrapper mb-4">
                                    <i class="bi bi-globe text-primary fs-3"></i>
                                </div>
                                <h5 class="fw-bold text-uppercase mb-3" style="letter-spacing: 1px; font-size: 1rem;">Univerzalni izgled</h5>
                                <p class="text-secondary small">Dizajnirani da izgledaju dobro bilo gde i da odgovaraju svakome — čisti, vanvremenski i nikada pretrpani.</p>
                            </div>
                        </div>
                    </div>
        
                    <!-- Kvalitet izrade -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm feature-card p-3">
                            <div class="card-body">
                                <div class="feature-icon-wrapper mb-4">
                                    <i class="bi bi-award text-primary fs-3"></i>
                                </div>
                                <h5 class="fw-bold text-uppercase mb-3" style="letter-spacing: 1px; font-size: 1rem;">Napravljeno da traje</h5>
                                <p class="text-secondary small">Kvalitetni materijali i rigorozno testiranje svakog proizvoda, kako bi izdržao godine intenzivnog korišćenja.</p>
                            </div>
                        </div>
                    </div>
        
                    <!-- Podrška -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm feature-card p-3">
                            <div class="card-body">
                                <div class="feature-icon-wrapper mb-4">
                                    <i class="bi bi-shield-check text-primary fs-3"></i>
                                </div>
                                <h5 class="fw-bold text-uppercase mb-3" style="letter-spacing: 1px; font-size: 1rem;">Bez briga</h5>
                                <p class="text-secondary small">Pouzdana garancija i podrška našeg tima gamera — igraj sa potpunim samopouzdanjem.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        

        <section class="newsletter-section bg-dark text-white py-5">
            <div class="container py-5">
                <div class="row justify-content-center text-center">
                    <div class="col-md-8 col-lg-6">
                       
                        <h2 class="fw-bold mb-3" style="letter-spacing: 1px; font-size: 2.5rem;">
                            Prijavite se na naš newsletter</h2>
                        
                        <p class="text-white mb-4" style="font-size: 1rem;">
                            Ne propustite naše najveće popuste i akcije.
                        </p>
        
                        <form action="#" method="POST" class="mt-4" onclick="event.preventDefault()">
                            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                                <div class="flex-grow-1 " style="max-width: 400px;">
                                    <input type="email" 
                                           name="email" 
                                           class="form-control newsletter-input text-primary fw-bold py-3 px-4" 
                                           placeholder="E-mail"
                                           style="border: 1px solid $primary; border-radius: 8px;">
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary newsletter-btn py-3 px-5 fw-bold text-uppercase">
                                        Prijavi se
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    
@endsection