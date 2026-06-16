@extends('layouts.admin')

@section('admin_content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold py-3">
                    <i class="bi bi-pencil-square me-2"></i>Dodaj Novu Vest
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data" id="newsForm" onsubmit="return syncNewsFormContent()">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Naslov Vesti</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Unesite naslov..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Gde će se prikazati?</label>
                            <select name="type" class="form-select border-primary text-primary fw-bold">
                                <option value="normal">Obična vest (Lista)</option>
                                <option value="hero">Hero Sekcija (Glavni Baner)</option>
                                <option value="promo">Promo (Manja kartica)</option>
                            </select>
                        </div>

                        <div class="mb-3 border rounded p-3 bg-light-subtle">
                            <label class="form-label fw-bold mb-3">
                                <i class="bi bi-sliders me-1 text-primary"></i> Hero sekcija – dodatna podešavanja
                            </label>
                            <div class="mb-3">
                                <label for="custom_url" class="form-label">Prilagođeni URL (Opciono)</label>
                                <input type="text" name="custom_url" id="custom_url" class="form-control"
                                       value="{{ old('custom_url') }}" placeholder="/pc-builder">
                                <small class="text-muted">Ako postavite URL, baner vodi direktno na tu stranicu umesto na vest.</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="dark_image" id="darkImage" value="1"
                                       {{ old('dark_image', 1) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="darkImage">
                                    <i class="bi bi-moon-fill me-1"></i> Tamna pozadinska slika
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">Uključeno = svetlo dugme i beli tekst. Isključeno = tamno dugme i crni tekst.</small>
                        </div>

                        <div class="mb-3 border rounded p-3 bg-light-subtle">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                                <label class="form-check-label fw-bold" for="isActive">
                                    <i class="bi bi-eye-fill me-1 text-success"></i> Status: Vest je aktivna
                                </label>
                            </div>
                            <small class="text-muted">Ako isključite ovo, vest se neće videti na sajtu.</small>
                        </div>


                        <div class="mb-3">
                            <label class="form-label fw-bold">Kratak opis (Summary)</label>
                            <textarea name="summary" class="form-control" rows="2" placeholder="Kratka rečenica..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Glavna Slika Vesti</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Slika Vesti za Mobilne Uređaje</label>
                            <input type="file" name="image_mobile" id="image_mobile" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Glavni Sadržaj</label>
                            
                            <div id="axon-pell-editor"></div>
                            
                            <textarea name="content" id="axon-pell-textarea" hidden>{{ old('content', $news->content ?? $product->content ?? '') }}</textarea>
                            
                            <div class="text-center">
                                <button type="button" id="axon-source-btn" class="btn btn-outline-dark mb-2 text-center mt-2">
                                    Source
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('news.index') }}" class="btn btn-light px-4 border">Odustani</a>
                            <button type="submit" class="btn btn-dark px-5 shadow">Objavi Vest</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.news._form_scripts')
@endsection