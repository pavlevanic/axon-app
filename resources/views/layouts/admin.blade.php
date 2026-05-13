<!doctype html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    

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
                            <div class="list-group list-group-flush">
                                <a href="{{ route('admin.index') }}" class="list-group-item list-group-item-action {{ Request::is('admin') ? 'bg-dark text-white' : '' }}">
                                  Dashboard
                                </a>
                                <a href="{{ route('product.index') }}" class="list-group-item list-group-item-action {{ Request::is('product*') ? 'bg-dark text-white' : '' }}">
                                  Upravljaj Proizvodima
                                </a>
                                <a href="{{ route('category.index') }}" class="list-group-item list-group-item-action {{ Request::is('category*') ? 'bg-dark text-white' : '' }}">
                                  Kategorije
                                </a>
                                <a href="{{ route('news.index') }}" class="list-group-item list-group-item-action {{ Route::is('news*') ? 'bg-dark text-white' : '' }}">
                                   Vesti
                                </a>
                            </div>
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
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
</body>
</html>
