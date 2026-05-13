@extends('layouts.admin')

@section('admin_content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Dodaj Novu Vest
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Naslov --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Naslov Vesti</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Unesite naslov..." required>
                        </div>

                        {{-- Tip Vesti --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Gde će se prikazati?</label>
                            <select name="type" class="form-select border-primary text-primary fw-bold">
                                <option value="normal">Obična vest (Lista)</option>
                                <option value="hero">Hero Sekcija (Glavni Baner)</option>
                                <option value="promo">Promo (Manja kartica)</option>
                            </select>
                            <small class="text-muted">Izaberite "Hero" ako želite da ovo bude prva stvar koju kupci vide.</small>
                        </div>

                        {{-- Kratak opis --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kratak opis (Summary)</label>
                            <textarea name="summary" class="form-control" rows="2" placeholder="Kratka rečenica koja privlači pažnju..."></textarea>
                        </div>

                        {{-- Glavni tekst --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Glavni Sadržaj</label>
                            <textarea id="summernote" name="content" class="form-control" rows="6" placeholder="Napišite celu vest ovde..." required></textarea>
                        </div>

                        {{-- Slika --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Slika Vesti</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('news.index') }}" class="btn btn-light px-4">Odustani</a>
                            <button type="submit" class="btn btn-dark px-5 shadow">Objavi Vest</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection