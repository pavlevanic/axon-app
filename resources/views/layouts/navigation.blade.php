<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
     
    <title>Document</title>
</head>
<body>
    @if(isset($regularNews) && $regularNews->count() > 0)
<div class="news-ticker-bar bg-light border-bottom d-none d-lg-block">
    <div class="container">
        <div id="newsTicker" class="carousel slide vertical" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($regularNews as $key => $news)
                <div class="carousel-item {{ $key == 0 ? 'active' : '' }} text-center py-2">
                    <a href="{{ route('news.show', $news->slug) }}" class="text-decoration-underline text-dark fw-medium small custom-ticker-text bold"> 
                        {{ $news->summary }}
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#newsTicker" data-bs-slide="prev">
                <i class="bi bi-chevron-left text-dark small"></i>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#newsTicker" data-bs-slide="next">
                <i class="bi bi-chevron-right text-dark small"></i>
            </button>
        </div>
    </div>
</div>
@endif
    <nav class="navbar navbar-expand-md navbar-dark bg-dark py-2">
        <div class="container">
            {{-- Logo --}}
            <a class="navbar-brand fw-bold text-white me-auto" href="{{ url('/') }}" style="letter-spacing: 2px; font-size: 1.5rem; on-hover">
                AXON
            </a>
            
            {{-- Mobile Icons (Vidljive samo na telefonima) --}}
            <div class="d-flex align-items-center d-md-none">
                <a class="nav-link text-white me-3" href="#" data-bs-toggle="collapse" data-bs-target="#searchCollapse">
                    <i class="bi bi-search fs-5"></i>
                </a>
            </div>
    
            <button class="navbar-toggler border-0 " type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon "></span>
            </button>
    
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                {{-- Centralni Linkovi --}}
                <ul class="navbar-nav mx-auto text-center">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-white text-uppercase small px-3" href="{{ route('shop.prebuilts') }}">Prebuild PCs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-white text-uppercase small px-3" href="{{ route('shop.components') }}">PC Komponente</a>
                    </li>
                    
                    {{-- Search ikona za Desktop --}}
                    <li class="nav-item d-none d-md-block">
                        <a class="nav-link text-white px-3" href="#" data-bs-toggle="collapse" data-bs-target="#searchCollapse">
                            <i class="bi bi-search"></i>
                        </a>
                    </li>
                </ul>
    
                {{-- Desna strana: Korpa i User --}}
                <ul class="navbar-nav align-items-center justify-content-center text-center">
                    
                    @auth
                        {{-- Korpa za Desktop --}}
                        @php
                            $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
                        @endphp
                        <li class="nav-item">
                            <a href="{{ route('cart.index') }}" class="nav-link text-white position-relative px-3">
                                <i class="bi bi-bag fs-5"></i>
                                @if($cartCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
    
                        {{-- Admin Link --}}
                        @if(auth()->user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link text-secondary small fw-bold text-uppercase px-3" href="/admin">Admin</a>
                            </li>
                        @endif
                    @endauth
    
                    {{-- Auth Sekcija --}}
                    @guest
                        <div class="d-flex flex-column flex-md-row align-items-center w-100 justify-content-center">
                            <li class="nav-item">
                                <a class="nav-link text-white small fw-bold text-uppercase px-2" href="{{ route('login') }}">Login</a>
                            </li>
                            <li class="nav-item ms-md-2 mt-2 mt-md-0">
                                <a class="btn btn-white btn-sm rounded-0 px-3 fw-bold text-uppercase" href="{{ route('register') }}">Register</a>
                            </li>
                        </div>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle text-white fw-bold px-3" href="#" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end rounded-0 border-dark text-center text-md-start bg-dark">
                                <a class="dropdown-item dropdown-item-dark small text-uppercase fw-bold text-white" href="{{ route('logout') }}"
                                   onmouseover="this.style.backgroundColor='#444'"
                                   onmouseout="this.style.backgroundColor='transparent'"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="collapse border-bottom border-black bg-black" id="searchCollapse">
        <div class="container py-3 ">
            <form action="{{ route('products.search') }}" method="GET">
                <div class="input-group text-white">
                    <input type="text" name="query" class="form-control rounded-0  " 
                           placeholder="Šta tražite danas?" value="{{ request('query') }}" required>
                    <button class="btn btn-dark rounded-0 px-4" type="submit">Pretraži</button>
                </div>
            </form>
        </div>
    </div>
    
    <style>
        .nav-link {
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #444 !important;
        }
        .navbar-brand:hover {
            color: #bdbdbd !important;
        }
    </style>
</body>
</html>