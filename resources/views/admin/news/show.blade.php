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

            <div class="position-relative mb-4 axon-show-wrapper" style="overflow: hidden; border-radius: 0.375rem;">
                
                <picture>
                    <source media="(max-width: 767.98px)" srcset="{{ asset($news->image_mobile ?? $news->image) }}">
                    <img src="{{ asset($news->image) }}" class="img-fluid w-100 shadow axon-show-img" style="max-height: 500px; object-fit: cover;">
                </picture>
                
                <div class="position-absolute axon-show-content p-4 p-lg-5" style="border-radius: 0.375rem 0 0 0.375rem; z-index: 2;">
                    <h1 class="{{ $news->dark_image ? 'text-white' : 'text-black' }} fw-bold display-6 mb-2 axon-show-title" style="max-width: 700px">{{ $news->title }}</h1>
                    <p class="{{ $news->dark_image ? 'text-white' : 'text-black' }} mb-0 axon-show-summary" style="opacity: 0.85; max-width: 700px;">{{ $news->summary }}</p>
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