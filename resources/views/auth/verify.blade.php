@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-0">
                <div class="card-header bg-dark text-white fw-bold py-3 text-uppercase text-center small tracking-wider" style="letter-spacing: 1px;">
                    <i class="bi bi-envelope-check me-2"></i>{{ __('Verifikacija Email Adrese') }}
                </div>

                <div class="card-body p-4 text-center">
                    @if (session('resent'))
                        <div class="alert alert-success rounded-0 border-0 shadow-sm mb-4 small d-flex align-items-center justify-content-center gap-2" role="alert">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>{{ __('Nov link za verifikaciju je uspešno poslat na vašu email adresu.') }}</span>
                        </div>
                    @endif

                    <div class="mb-4 text-muted">
                        <i class="bi bi-shield-lock display-4 text-dark d-block mb-3"></i>
                        <p class="mb-2 fw-medium text-dark" style="font-size: 0.95rem;">
                            {{ __('Pre nego što nastavite, molimo vas da proverite vašu email poštu za verifikacioni link.') }}
                        </p>
                        <small class="d-block text-muted">
                            {{ __('Ukoliko niste primili email, možete generisati novi zahtev klikom na dugme ispod.') }}
                        </small>
                    </div>

                    <form class="d-block" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark rounded-0 fw-bold btn-sm py-2 text-uppercase shadow-sm tracking-wider" style="letter-spacing: 0.5px;">
                                <i class="bi bi-arrow-clockwise me-1"></i> {{ __('Pošalji ponovo') }}
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer bg-white border-0 text-center pb-4 pt-0">
                    <a href="/" class="text-decoration-none small text-muted hover-primary">
                        <i class="bi bi-arrow-left me-1"></i> Povratak na početnu
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Suptilni hover efekat za link na dnu */
    .hover-primary:hover {
        color: #0d6efd !important;
        transition: color 0.2s ease-in-out;
    }
</style>
@endsection