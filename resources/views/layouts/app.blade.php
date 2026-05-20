<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO & Metadata -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="AXON - Vrhunske PC konfiguracije i komponente za entuzijaste.">
    <meta name="author" content="AXON">
    
    <title>{{ config('app.name', 'AXON | Premium PC Build') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Scripts & Styles (Vite) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>
    @stack('styles')
</head>
<body>
    <div id="app">
        @include('layouts.navigation')
        
        <main>
            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="container mt-3">
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $error }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endforeach
                </div>
            @endif

            
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            @yield('content')

            <footer class="bg-dark text-white pt-0 border-top border-secondary">
                <button onclick="scrollToTop()" class="btn btn-primary w-100 rounded-0 py-3 fw-bold text-uppercase border-0" style="letter-spacing: 2px; font-size: 0.8rem;">
                    <i class="bi bi-arrow-up me-2"></i> Povratak na vrh
                </button>
            
                <div class="container py-5">
                    <div class="row g-4">
                        <div class="col-md-4 text-center text-md-start">
                            <h4 class="fw-bold mb-3" style="letter-spacing: 2px;">AXON</h4>
                            <p class="text-secondary small">Vrhunske PC konfiguracije i komponente za entuzijaste. Snaga i preciznost u svakom bitu.</p>
                            <div class="d-flex justify-content-center justify-content-md-start gap-3 mt-3">
                                <a href="#" class="text-primary fs-5"><i class="bi bi-instagram"></i></a>
                                <a href="#" class="text-primary fs-5"><i class="bi bi-facebook"></i></a>
                                <a href="#" class="text-primary fs-5"><i class="bi bi-twitter-x"></i></a>
                            </div>
                        </div>
            
                        <div class="col-6 col-md-2 offset-md-2 text-center text-md-start">
                            <h6 class="text-uppercase fw-bold mb-3 small">Shop</h6>
                            <ul class="list-unstyled small text-secondary">
                                <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Prebuild PCs</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">PC Komponente</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Akcije</a></li>
                            </ul>
                        </div>
            
                        <div class="col-6 col-md-2 text-center text-md-start">
                            <h6 class="text-uppercase fw-bold mb-3 small">Podrška</h6>
                            <ul class="list-unstyled small text-secondary">
                                <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Kontakt</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Dostava</a></li>
                                <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Reklamacije</a></li>
                            </ul>
                        </div>
            
                        <div class="col-md-2 text-center text-md-start">
                            <h6 class="text-uppercase fw-bold mb-3 small">Kontakt</h6>
                            <p class="text-secondary small mb-1">Kosovska Mitrovica</p>
                            <p class="text-secondary small">support@axon.com</p>
                        </div>
                    </div>
                </div>
            
                <div class="border-top border-dark py-3">
                    <div class="container text-center">
                        <p class="mb-0 small text-secondary" style="font-size: 0.7rem;">
                            &copy; {{ date('Y') }} <span class="fw-bold">AXON</span>. Sva prava zadržana.
                        </p>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>
</html>