@extends('layouts.admin')

@section('admin_content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-pencil-square me-2 text-warning"></i>Izmeni kategoriju: {{ $category->name }}
                </h5>
                <a href="{{ route('category.index') }}" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Nazad na listu
                </a>
            </div>

            <div class="card-body p-4">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('category.update', $category) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') 

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="name" class="form-label fw-bold">Naziv kategorije</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" 
                                   id="name"
                                   value="{{ old('name', $category->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="slug" class="form-label fw-bold">Slug (URL putanja)</label>
                            <input type="text" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   name="slug" 
                                   id="slug"
                                   value="{{ old('slug', $category->slug) }}" 
                                   required>
                            <small class="text-muted text-truncate d-block">Trenutni link: {{ url('/category/'.$category->slug) }}</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold d-block">Slika kategorije</label>
                        <div class="d-flex align-items-start gap-3">
                            @if($category->image)
                                <div class="text-center">
                                    {{-- POPRAVLJENO: Dodat 'storage/' prefiks --}}
                                    <img src="{{ asset('storage/' . $category->image) }}" 
                                         alt="{{ $category->name }}" 
                                         class="img-thumbnail shadow-sm mb-2" 
                                         style="max-width: 150px; height: 100px; object-fit: cover;">
                                    <small class="d-block text-muted">Trenutna slika</small>
                                </div>
                            @endif
                            
                            <div class="flex-grow-1">
                                <label for="image" class="form-label small">Zameni sliku (ostavi prazno ako ne menjaš)</label>
                                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4 border-light shadow-sm">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-uppercase small fw-bold">Grupisanje Atributa (Specifikacije)</h6>
                            <button type="button" class="btn btn-sm btn-light fw-bold" onclick="addGroup()">+ Dodaj Grupu</button>
                        </div>
                        <div class="card-body bg-light" id="attribute-groups-container">
                            @if($category->attribute_groups)
                                @foreach($category->attribute_groups as $groupName => $attrs)
                                    <div class="row mb-3 group-row border-bottom pb-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Naziv Grupe</label>
                                            <input type="text" name="group_names[]" class="form-control" value="{{ $groupName }}">
                                        </div>
                                        <div class="col-md-7">
                                            <label class="form-label small fw-bold">Atributi (odvojeni zarezom)</label>
                                            <input type="text" name="group_attributes[]" class="form-control" value="{{ implode(', ', $attrs) }}">
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

                    <div class="mt-4 mb-4">
                        <label for="desc" class="form-label fw-bold">Opis kategorije</label>
                        <textarea name="desc" 
                                  id="desc" 
                                  rows="5" 
                                  class="form-control @error('desc') is-invalid @enderror">{{ old('desc', $category->desc) }}</textarea>
                        @error('desc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-4">
                        <a href="{{ route('category.index') }}" class="btn btn-outline-dark px-4 me-md-2">Otkaži</a>
                        <button type="submit" class="btn btn-dark px-5 fw-bold shadow">
                            <i class="bi bi-save me-1"></i> Sačuvaj izmene
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Automatsko generisanje sluga na osnovu imena
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    nameInput.addEventListener('keyup', function() {
        // Generiše slug samo ako je polje prazno (da ne bi kvarili postojeće linkove namerno)
        // Ako želiš da se stalno menja, izbaci if uslov
        let slug = nameInput.value.toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
        
        // slugInput.value = slug; // Otkomentariši ako želiš auto-update
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
                    <input type="text" name="group_attributes[]" class="form-control" placeholder="npr. Model, Takt">
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