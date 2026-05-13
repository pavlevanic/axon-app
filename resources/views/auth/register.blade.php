@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6">
            {{-- Naslov u AXON stilu --}}
            <div class="text-center mb-5">
                <h1 class="display-6 fw-bold text-dark">KREIRAJ NALOG</h1>
                <p class="text-secondary">Pridružite se AXON zajednici</p>
            </div>

            <div class="card border-0 shadow-sm p-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold text-dark small uppercase">{{ __('Name') }}</label>
                            <input id="name" type="text" 
                                   class="form-control form-control-lg rounded-0 border-dark-subtle @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" 
                                   required autocomplete="name" autofocus
                                   placeholder="Vaše ime">

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold text-dark small uppercase">{{ __('Email Address') }}</label>
                            <input id="email" type="email" 
                                   class="form-control form-control-lg rounded-0 border-dark-subtle @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" 
                                   required autocomplete="email"
                                   placeholder="ime@primer.com">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="password" class="form-label fw-bold text-dark small uppercase">{{ __('Lozinka') }}</label>
                                <input id="password" type="password" 
                                       class="form-control form-control-lg rounded-0 border-dark-subtle @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="new-password"
                                       placeholder="••••••••">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="password-confirm" class="form-label fw-bold text-dark small uppercase">{{ __('Potvrdi Lozinku') }}</label>
                                <input id="password-confirm" type="password" 
                                       class="form-control form-control-lg rounded-0 border-dark-subtle" 
                                       name="password_confirmation" required autocomplete="new-password"
                                       placeholder="••••••••">
                            </div>
                        </div>

                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-dark btn-lg rounded-0 py-3 fw-bold shadow-sm">
                                {{ __('REGISTER') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-secondary small">Već imate nalog? 
                    <a href="{{ route('login') }}" class="text-dark fw-bold text-decoration-none">Prijavite se</a>
                </p>
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
    .form-control-lg {
        font-size: 1rem;
    }
</style>
@endsection