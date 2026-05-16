@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-0">
                <div class="card-header bg-dark text-white fw-bold py-3 text-uppercase text-center small tracking-wider" style="letter-spacing: 1px;">
                    <i class="bi bi-key-fill me-2"></i>{{ __('Zaboravljena Lozinka') }}
                </div>

                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success rounded-0 border-0 shadow-sm mb-4 small d-flex align-items-center justify-content-center gap-2" role="alert">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <div class="text-muted small text-center mb-4">
                        {{ __('Unesite vašu email adresu i poslaćemo vam link za resetovanje lozinke.') }}
                    </div>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label small fw-bold text-uppercase text-muted">{{ __('Email Adresa') }}</label>
                            <input id="email" type="email" 
                                   class="form-control rounded-0 @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" 
                                   required autocomplete="email" autofocus 
                                   placeholder="primer@domen.com">

                            @error('email')
                                <span class="invalid-feedback rounded-0" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-0 fw-bold btn-sm py-2 text-uppercase shadow-sm tracking-wider" style="letter-spacing: 0.5px;">
                                {{ __('Pošalji link za reset') }}
                            </button>
                            
                            <a href="{{ route('login') }}" class="btn btn-link text-decoration-none small text-muted text-center mt-2">
                                <i class="bi bi-arrow-left me-1"></i> Nazad na prijavu
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection