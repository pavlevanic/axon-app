@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex mb-4">
        @if(Route::currentRouteName() != 'shop.prebuilts' && count($categories) > 0)
        <button id="toggleBtn" class="btn btn-dark shadow-sm d-flex align-items-center gap-2 transition-all" 
                onclick="toggleSidebar()" 
                type="button"
                style="z-index: 1050; border-radius: 0;">
            <i class="bi bi-sliders"></i> 
            <span id="btn-text">Filteri i Kategorije</span>
        </button>
        @else
        <button id="toggleBtn" class="btn btn-dark shadow-sm d-flex align-items-center gap-2 transition-all" 
                onclick="toggleSidebar()" 
                type="button"
                style="z-index: 1050; border-radius: 0;">
            <i class="bi bi-sliders"></i> 
            <span id="btn-text">Filteri</span>
        @endif
    </div>

    <div class="row g-4">
        <div id="sidebar-column" class="col-lg-3 transition-all">
            <div id="sidebar-container">
                <form action="{{ URL::current() }}" method="GET" id="filterForm">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif

                    {{-- Dinamičke kategorije --}}
                    @if(Route::currentRouteName() != 'shop.prebuilts' && count($categories) > 0)
                    <div class="card border-0 shadow-sm mb-3 rounded-0">
                        <div class="card-header bg-white border-0 py-3" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#catCollapse">
                            <h6 class="fw-bold mb-0 d-flex justify-content-between align-items-center text-uppercase small">
                                Kategorije <i class="bi bi-chevron-down"></i>
                            </h6>
                        </div>
                        <div id="catCollapse" class="collapse show">
                            <div class="card-body pt-0">
                                <ul class="list-unstyled mb-0">
                                    @foreach($categories as $cat)
                                    <li class="mb-2">
                                        <a href="{{ route('shop.components', ['category' => $cat->slug]) }}" 
                                           class="text-decoration-none text-dark {{ request('category') == $cat->slug ? 'fw-bold text-primary' : '' }}">
                                           {{ $cat->name }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Dinamički Spec Filteri (Top 4 atributa) --}}
                    @if(isset($allSpecs) && count($allSpecs) > 0)
                        @foreach($allSpecs as $specName => $values)
                        <div class="card border-0 shadow-sm mb-2 rounded-0">
                            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center" 
                                 style="cursor: pointer;" 
                                 data-bs-toggle="collapse" 
                                 data-bs-target="#collapse{{ Str::slug($specName) }}">
                                <span class="fw-bold text-uppercase" style="font-size: 0.75rem;">{{ $specName }}</span>
                                <i class="bi bi-chevron-down small"></i>
                            </div>
                            <div id="collapse{{ Str::slug($specName) }}" class="collapse {{ request('specs.'.$specName) ? 'show' : '' }}">
                                <div class="card-body pt-0">
                                    @foreach($values as $value)
                                    <div class="filter-item-wrapper">
                                        <input type="checkbox" 
                                               class="custom-check-input"
                                               name="specs[{{ $specName }}][]" 
                                               value="{{ $value }}" 
                                               id="check-{{ Str::slug($specName.$value) }}"
                                               {{ is_array(request("specs.$specName")) && in_array($value, request("specs.$specName")) ? 'checked' : '' }}
                                               onchange="this.form.submit()">
                                        
                                        <label class="filter-label" for="check-{{ Str::slug($specName.$value) }}">
                                            {{ $value }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif

                
                    <div class="card border-0 shadow-sm mb-3 rounded-0 p-3">
                        <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.75rem;">Cena (€)</h6>
                        
                        <div id="price-slider" class="mb-4 mx-2"></div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" id="min_price" name="min_price" 
                                   class="form-control form-control-sm text-center rounded-0" 
                                   placeholder="Min" value="{{ request('min_price', 0) }}">
                            <span class="text-muted">—</span>
                            <input type="number" id="max_price" name="max_price" 
                                   class="form-control form-control-sm text-center rounded-0" 
                                   placeholder="Max" value="{{ request('max_price', 5000) }}">
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3 rounded-0 p-3">
                        <div class="filter-item-wrapper mb-2">
                            <input type="checkbox" class="custom-check-input" name="in_stock" id="stock" 
                                   {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="filter-label" for="stock">Samo na stanju</label>
                        </div>
                        
                        <div class="filter-item-wrapper">
                            <input type="checkbox" class="custom-check-input" name="on_sale" id="sale" 
                                   {{ request('on_sale') ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="filter-label" for="sale">Akcija/Popust</label>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ URL::current() }}" class="btn btn-light btn-sm rounded-0 border text-uppercase fw-bold shadow-sm">Poništi sve</a>
                    </div>
                </form>
            </div>
        </div>

        <div id="content-column" class="col-lg-9 transition-all">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">{{ $viewTitle }}</h2>
                <small class="text-muted">Prikazano: {{ $products->count() }} od {{ $products->total() }}</small>
            </div>

            <div class="row g-4" id="products-grid">
                @forelse($products as $product)
                    @php
                        $hasDiscount = $product->discount_price > 0 && $product->discount_price < $product->price;
                        if ($hasDiscount) {
                            $percentage = round(100 - ($product->discount_price / $product->price * 100));
                        }
                        $isOutOfStock = $product->stock <= 0;
                    @endphp

                    <div class="col-6 col-md-4 product-item transition-all">
                        <div class="card h-100 border-0 shadow-sm product-card transition {{ $isOutOfStock ? 'opacity-75' : '' }}">
                            
                            <div class="position-absolute top-0 start-0 m-2" style="z-index: 2;">
                                @if($isOutOfStock)
                                    <span class="badge bg-secondary rounded-0 px-2 py-1 fw-bold shadow-sm">RASPRODATO</span>
                                @elseif($hasDiscount)
                                    <span class="badge bg-danger rounded-0 px-2 py-1 fw-bold shadow-sm">-{{ $percentage }}%</span>
                                @else
                                    <span class="badge bg-dark rounded-0 px-2 py-1 fw-bold shadow-sm">NOVO</span>
                                @endif
                            </div>

                            <div class="p-3 text-center bg-light position-relative overflow-hidden">
                                <a href="{{ route('product.show', $product->slug) }}" class="d-block">
                                    <img src="{{ asset($product->image) }}" 
                                         class="img-fluid {{ $isOutOfStock ? 'grayscale-filter' : '' }}" 
                                         style="height: 180px; object-fit: contain; transition: transform 0.3s ease;" 
                                         alt="{{ $product->name }}">
                                </a>
                            </div>

                            <div class="card-body d-flex flex-column p-3">
                                <small class="text-muted text-uppercase mb-1" style="font-size: 0.65rem;">
                                    {{ $product->category->name ?? 'Komponente' }}
                                </small>
                                <h6 class="card-title fw-bold mb-3" style="font-size: 0.9rem; line-height: 1.4;">
                                    <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-dark">
                                        {{ Str::limit($product->name, 45) }}
                                    </a>
                                </h6>
                                
                                <div class="mt-auto">
                                    <div class="d-flex flex-column mb-3">
                                        @if($isOutOfStock)
                                            <span class="fs-5 fw-bold text-muted">Nedostupno</span>
                                        @elseif($hasDiscount)
                                            <span class="text-muted text-decoration-line-through small">
                                                {{ number_format($product->price, 2, ',', '.') }} €
                                            </span>
                                            <span class="fs-5 fw-bold text-danger">
                                                {{ number_format($product->discount_price, 2, ',', '.') }} €
                                            </span>
                                        @else
                                            <span class="fs-5 fw-bold text-dark">
                                                {{ number_format($product->price, 2, ',', '.') }} €
                                            </span>
                                        @endif
                                    </div>

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('product.show', $product->slug) }}" class="btn btn-outline-dark rounded-0 fw-bold btn-sm">DETALJI</a>
                                        @if(!$isOutOfStock)
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-dark rounded-0 w-100 fw-bold btn-sm">
                                                    <i class="bi bi-cart3"></i> U KORPU
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-cpu display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Nema proizvoda sa odabranim kriterijumima.</h4>
                    </div>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar-column');
    const content = document.getElementById('content-column');
    const btnText = document.getElementById('btn-text');
    const toggleBtn = document.getElementById('toggleBtn');
    const productsGrid = document.getElementById('products-grid');

    if (sidebar.classList.contains('d-none')) {
        sidebar.classList.remove('d-none');
        content.classList.replace('col-lg-12', 'col-lg-9');
        productsGrid.classList.remove('full-width-grid');
        btnText.classList.remove('d-none');
        toggleBtn.classList.replace('btn-outline-dark', 'btn-dark');
        toggleBtn.style.padding = "0.375rem 0.75rem";
    } else {
        sidebar.classList.add('d-none');
        content.classList.replace('col-lg-9', 'col-lg-12');
        productsGrid.classList.add('full-width-grid');
        btnText.classList.add('d-none');
        toggleBtn.classList.replace('btn-dark', 'btn-outline-dark');
        toggleBtn.style.padding = "0.375rem 0.5rem";
    }
}
</script>
@endsection