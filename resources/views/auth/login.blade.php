@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-5">
            <div class="text-center mb-5">
                <h1 class="display-6 fw-bold text-dark">AXON</h1>
                <p class="text-secondary">Prijavite se na svoj nalog</p>
            </div>

            <div class="card border-0 shadow-sm p-4"> 
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold text-dark small uppercase">{{ __('Email Address') }}</label>
                            <input id="email" type="email" 
                                   class="form-control form-control-lg rounded-0 border-dark-subtle @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" 
                                   required autocomplete="email" autofocus
                                   placeholder="ime@primer.com">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold text-dark small uppercase">{{ __('Password') }}</label>
                            <input id="password" type="password" 
                                   class="form-control form-control-lg rounded-0 border-dark-subtle @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="current-password"
                                   placeholder="••••••••">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input border-dark" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-secondary" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark btn-lg rounded-0 py-3 fw-bold shadow-sm">
                                {{ __('Prijavi se') }}
                            </button>

                            @if (Route::has('password.request'))
                                <a class="btn btn-link text-dark text-decoration-none small text-center mt-2" href="{{ route('password.request') }}">
                                    {{ __('Zaboravljena lozinka?') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="text-secondary text-decoration-none small">
                    ← Nazad na početnu stranicu
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus {
        border-color: #212529;
        box-shadow: none;
    }
    .uppercase {
        text-transform: uppercase;
        letter-spacing: 1px;
    }
</style>
@endsection