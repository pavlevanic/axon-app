@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-0">
                <div class="card-header bg-dark text-white fw-bold py-3 text-uppercase text-center small tracking-wider" style="letter-spacing: 1px;">
                    <i class="bi bi-shield-key me-2"></i>{{ __('Resetovanje Lozinke') }}
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold text-uppercase text-muted">{{ __('Email Adresa') }}</label>
                            <input id="email" type="email" 
                                   class="form-control rounded-0 @error('email') is-invalid @enderror" 
                                   name="email" value="{{ $email ?? old('email') }}" 
                                   required autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback rounded-0" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label small fw-bold text-uppercase text-muted">{{ __('Nova Lozinka') }}</label>
                            <input id="password" type="password" 
                                   class="form-control rounded-0 @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="new-password">

                            @error('password')
                                <span class="invalid-feedback rounded-0" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label small fw-bold text-uppercase text-muted">{{ __('Potvrdi Lozinku') }}</label>
                            <input id="password-confirm" type="password" 
                                   class="form-control rounded-0" 
                                   name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-0 fw-bold btn-sm py-2 text-uppercase shadow-sm tracking-wider" style="letter-spacing: 0.5px;">
                                {{ __('Ažuriraj Lozinku') }}
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