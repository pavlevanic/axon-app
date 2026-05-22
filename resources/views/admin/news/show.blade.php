@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Početna</a></li>
                    <li class="breadcrumb-item active">{{ $news->title }}</li>
                </ol>
            </nav>

            <div class="position-relative mb-4">
                <img src="{{ asset($news->image) }}" 
                     class="img-fluid w-100 rounded shadow" 
                     style="max-height: 500px; object-fit: cover;">
                
                <div class="position-absolute top-50 start-0 translate-middle-y p-4 p-lg-5" 
                     style="background: linear-gradient(to right, rgba(0,0,0,0.7), transparent); border-radius: 0.375rem 0 0 0.375rem;">
                    <h1 class="text-white fw-bold display-6 mb-2">{{ $news->title }}</h1>
                    <p class="text-white mb-0" style="opacity: 0.85; max-width: 700px;">{{ $news->summary }}</p>
                </div>
            </div>
            
            
            <h1 class="display-4 fw-bold mb-3">{{ $news->title }}</h1>
            <p class="text-muted mb-4">Objavljeno: {{ $news->created_at->format('d.m.Y.') }}</p>
            
            <div class="news-content fs-5 lh-lg">
                {!! $news->content !!}
            </div>
            
            <hr class="my-5">
            <a href="/" class="btn btn-dark px-4 py-2">Nazad na početnu</a>
        </div>
    </div>
</div>
@endsection