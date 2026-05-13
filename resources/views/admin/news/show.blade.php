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

            <img src="{{ asset($news->image) }}" class="img-fluid w-100 rounded shadow mb-4" style="max-height: 500px; object-fit: cover;">
            
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