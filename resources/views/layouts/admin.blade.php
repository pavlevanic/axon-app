<!doctype html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        .admin-sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .admin-sidebar-nav .nav-link {
            text-decoration: none !important;
            padding: 0.65rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            color: #212529;
            transition: background-color 0.15s, color 0.15s;
        }
        .admin-sidebar-nav .nav-link:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.08);
            color: var(--bs-primary) !important;
        }
        .admin-sidebar-nav .nav-link.active {
            background-color: var(--bs-primary);
            color: #fff !important;
        }
    </style>
</head>
<body>
    <div id="app">
        @include('layouts.navigation') 

        <div class="container py-5">
            <div class="row">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Admin Meni</h5>
                            <nav class="nav nav-pills flex-column admin-sidebar-nav">
                                <a href="{{ route('admin.index') }}"
                                   class="nav-link {{ Request::is('admin') && !Request::is('admin/*') ? 'active' : '' }}">
                                    Dashboard
                                </a>
                                <a href="{{ route('product.index') }}"
                                   class="nav-link {{ Request::is('product*') ? 'active' : '' }}">
                                    Upravljaj Proizvodima
                                </a>
                                <a href="{{ route('category.index') }}"
                                   class="nav-link {{ Request::is('category*') ? 'active' : '' }}">
                                    Kategorije
                                </a>
                                <a href="{{ route('news.index') }}"
                                   class="nav-link {{ Route::is('news*') ? 'active' : '' }}">
                                    Vesti
                                </a>
                                <a href="{{ route('builder-products.index') }}"
                                   class="nav-link {{ Route::is('builder-products*') ? 'active' : '' }}">
                                    PC Builder Komponente
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    @yield('admin_content')
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    @stack('scripts')
</body>
</html>
