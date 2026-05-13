@extends('layouts.admin') {{-- Prelazimo na admin layout --}}

@section('admin_content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-plus-circle me-2 text-success"></i>Dodaj novu kategoriju
                </h5>
                <a href="{{ route('category.index') }}" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Nazad na listu
                </a>
            </div>

            <div class="card-body p-4">
                {{-- Prikaz grešaka ako postoje --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Naziv kategorije</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               name="name" 
                               id="name"
                               placeholder="Unesite naziv (npr. Laptopovi)"
                               value="{{ old('name') }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label fw-bold">Slika kategorije</label>
                        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
                        <small class="text-muted">Preporučeni format: JPG ili PNG, max 2MB.</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="slug" class="form-label fw-bold">Slug (URL putanja)</label>
                        <input type="text" 
                               class="form-control @error('slug') is-invalid @enderror" 
                               name="slug" 
                               id="slug"
                               placeholder="laptopovi-novi"
                               value="{{ old('slug') }}" 
                               required>
                        <small class="text-muted">Slug se automatski generiše iz naziva, ali ga možete izmeniti.</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card mt-4 mb-4">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Grupisanje Atributa (Opciono)</h6>
                            <button type="button" class="btn btn-sm btn-light" onclick="addGroup()">+ Dodaj Grupu</button>
                        </div>
                        <div class="card-body" id="attribute-groups-container">
                            {{-- Ako je bilo grešaka pri slanju, vraćamo prethodno unete grupe --}}
                            @if(old('group_names'))
                                @foreach(old('group_names') as $index => $name)
                                    <div class="row mb-3 group-row border-bottom pb-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Naziv Grupe</label>
                                            <input type="text" name="group_names[]" class="form-control" value="{{ $name }}">
                                        </div>
                                        <div class="col-md-7">
                                            <label class="form-label small fw-bold">Atributi (odvojeni zarezom)</label>
                                            <input type="text" name="group_attributes[]" class="form-control" value="{{ old('group_attributes')[$index] }}">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.group-row').remove()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="desc" class="form-label fw-bold">Opis kategorije</label>
                        <textarea name="desc" 
                                  id="desc" 
                                  rows="5" 
                                  placeholder="Kratak opis kategorije..."
                                  class="form-control @error('desc') is-invalid @enderror">{{ old('desc') }}</textarea>
                        @error('desc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-4">
                        <a href="{{ route('category.index') }}" class="btn btn-outline-dark px-4 me-md-2">Otkaži</a>
                        <button type="submit" class="btn btn-dark px-5 fw-bold">
                            <i class="bi bi-check-circle me-1"></i> Kreiraj kategoriju
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    // Automatsko generisanje sluga
    nameInput.addEventListener('input', function() {
        let slug = this.value.toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
        slugInput.value = slug;
    });

    function addGroup() {
        const container = document.getElementById('attribute-groups-container');
        const html = `
            <div class="row mb-3 group-row border-bottom pb-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Naziv Grupe</label>
                    <input type="text" name="group_names[]" class="form-control" placeholder="npr. Procesor">
                </div>
                <div class="col-md-7">
                    <label class="form-label small fw-bold">Atributi (odvojeni zarezom)</label>
                    <input type="text" name="group_attributes[]" class="form-control" placeholder="npr. Model, Broj jezgara, Takt">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.group-row').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }
</script>
@endsection