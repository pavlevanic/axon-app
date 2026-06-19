@extends('layouts.admin') 

@section('admin_content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Dodaj novi AXON proizvod</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Naziv proizvoda</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Unesite naziv proizvoda">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Slug (URL putanja)</label>
                            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}" placeholder="automatski-generisano">
                            <small class="text-muted">Primer: moji-novi-proizvod</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kratak opis (Short Desc)</label>
                            <textarea name="short_desc" class="form-control" rows="2">{{ old('short_desc') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Glavni Opis Proizvoda</label>
                            
                            <div id="axon-pell-editor" class="border rounded bg-white"></div>
                            
                            <textarea name="desc" id="axon-pell-textarea" class="form-control" style="font-family: monospace; min-height: 200px;" hidden>{{ old('desc') }}</textarea>
                            
                            <div class="text-center mt-2">
                                <button type="button" id="axon-source-btn" class="btn btn-outline-dark">
                                    Source
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategorija</label>
                            <select name="category_id" id="category_id" class="form-select" onchange="window.location.href = '?category_id=' + this.value">
                                <option value="">Izaberi kategoriju</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if(request('category_id') == null)
                                <small class="text-danger">Izaberite kategoriju da učitate specifikacije.</small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Cena</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Popust Cena (Opciono)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="discount_price" class="form-control" value="{{ old('discount_price') }}">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Stanje na zalihama (Stock)</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status Proizvoda</label>
                            <select name="status" class="form-select">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Skriveno)</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Objavljeno (Vidljivo)</option>
                            </select>
                        </div>

                        <div class="card border-primary mb-3">
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="isFeatured">Istaknut proizvod</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mb-4">
                    <label class="form-label fw-bold">Slike za galeriju</label>
                    <input type="file" name="images[]" class="form-control" multiple required>
                    <small class="text-muted">Prva slika iz niza će automatski postati naslovna slika.</small>
                </div>

                @php
                    $selectedCategory = $categories->where('id', request('category_id'))->first();
                @endphp

<div class="card mt-4 mb-4">
    <div class="card-header bg-dark text-white">
        <h6 class="mb-0">Tehničke Specifikacije @if($selectedCategory) (Kategorija: {{ $selectedCategory->name }}) @endif</h6>
    </div>
    <div class="card-body">
        @if($selectedCategory)
            @if($selectedCategory->attribute_groups && count($selectedCategory->attribute_groups) > 0)
                @foreach($selectedCategory->attribute_groups as $groupName => $attributes)
                    <div class="group-section mb-4">
                        <h7 class="fw-bold text-primary border-bottom d-block pb-1 mb-3">
                            <i class="bi bi-gear-fill me-1"></i> {{ $groupName }}
                        </h7>
                        <div class="row">
                            @foreach($attributes as $attr)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small">{{ $attr }}</label>
                                    <input type="text" 
                                           name="specs[{{ $groupName }}_{{ $attr }}]" 
                                           value="{{ old("specs.{$groupName}_{$attr}") }}" 
                                           class="form-control form-control-sm" 
                                           placeholder="Unesi {{ strtolower($attr) }}...">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            
            @elseif($selectedCategory->attribute_names)
                <div class="row">
                    @foreach($selectedCategory->attribute_names as $attr)
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ $attr }}</label>
                            <input type="text" 
                                   name="specs[{{ $attr }}]" 
                                   value="{{ old("specs.$attr") }}" 
                                   class="form-control" 
                                   placeholder="Unesi {{ strtolower($attr) }}...">
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <p class="text-muted italic">Molimo izaberite kategoriju da biste videli polja za specifikacije.</p>
        @endif
    </div>
</div>

                <div class="text-end">
                    <a href="{{ route('product.index') }}" class="btn btn-light border">Odustani</a>
                    <button type="submit" class="btn btn-dark px-4">Kreiraj proizvod</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="sourceModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
  
        <div class="modal-header">
          <h5 class="modal-title">HTML Source Editor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
  
        <div class="modal-body">
          <textarea id="htmlSource" style="width:100%;height:500px;font-family:monospace;"></textarea>
        </div>
  
        <div class="modal-footer">
          <button class="btn btn-outline-danger" data-bs-dismiss="modal">Nazad</button>
          <button class="btn btn-primary" onclick="applySource()">Potvrdi</button>
        </div>
  
      </div>
    </div>
  </div>
  <script>
    let sourceModal;
    document.addEventListener('DOMContentLoaded', function () {
        sourceModal = new bootstrap.Modal(document.getElementById('sourceModal'));
        
    });
    
    document.getElementById('axon-source-btn').addEventListener('click', function() {
        const html = document.getElementById('axon-pell-textarea').value;
        document.getElementById('htmlSource').value = html;
        sourceModal.show();
    });
    
    function applySource() {
        const html = document.getElementById('htmlSource').value;
        
        document.getElementById('axon-pell-textarea').value = html;
        const pellContent = document.querySelector('.pell-content');
        if(pellContent) {
            pellContent.innerHTML = html;
        }
    
        sourceModal.hide();
    }
    
    document.querySelector('form[action="{{ route("product.store") }}"]').addEventListener('submit', function (e) {
        const pellContent = document.querySelector('.pell-content');
        if (pellContent) {
            document.getElementById('axon-pell-textarea').value = pellContent.innerHTML;
        }
    });
    </script>
    <script>
        document.getElementById('name').addEventListener('input', function() {
            let name = this.value;
            let slug = name.toLowerCase()
                           .replace(/[^\w ]+/g, '')
                           .replace(/ +/g, '-');
            document.getElementById('slug').value = slug;
        });
    </script>
@endsection