@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-0">
                <div class="card-header bg-dark text-white fw-bold py-3 text-uppercase text-center small tracking-wider" style="letter-spacing: 1px;">
                    <i class="bi bi-shield-lock-fill me-2"></i>{{ __('Potvrda Bezbednosti') }}
                </div>

                <div class="card-body p-4">
                    <div class="text-muted small text-center mb-4">
                        {{ __('Molimo potvrdite vašu lozinku pre nego što nastavite sa ovom osetljivom akcijom.') }}
                    </div>

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label small fw-bold text-uppercase text-muted mb-0">{{ __('Trenutna Lozinka') }}</label>
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link p-0 text-decoration-none small text-muted" href="{{ route('password.request') }}" style="font-size: 0.75rem;">
                                        {{ __('Zaboravili ste lozinku?') }}
                                    </a>
                                @endif
                            </div>
                            
                            <input id="password" type="password" 
                                   class="form-control rounded-0 @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="current-password" autofocus>

                            @error('password')
                                <span class="invalid-feedback rounded-0" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark rounded-0 fw-bold btn-sm py-2 text-uppercase shadow-sm tracking-wider" style="letter-spacing: 0.5px;">
                                {{ __('Potvrdi Lozinku') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection