@extends('layouts.admin') 

@section('admin_content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Izmeni AXON proizvod: {{ $product->name }}</h5>
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
            <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Naziv proizvoda</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Slug (URL putanja)</label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug', $product->slug) }}">
                            <small class="text-muted">Primer: moji-novi-proizvod</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kratak opis (Short Desc)</label>
                            <textarea name="short_desc" class="form-control" rows="2">{{ old('short_desc', $product->short_desc) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Detaljan opis</label>
                            <textarea name="desc" id="editor" class="form-control" rows="5">{{ old('desc', $product->desc) }}</textarea>
                            <div class="text-center">
                                <button type="button" class="btn btn-outline-dark mb-2 text-center mt-1" onclick="openSourceEditor()">
                                    <>Source
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategorija</label>
                            <select name="category_id" class="form-select">
                                <option value="">Izaberi kategoriju</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cena</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Popust Cena (Discount)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="discount_price" class="form-control" value="{{ old('discount_price', $product->discount_price) }}">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Stanje na zalihama (Stock)</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status Proizvoda</label>
                            <select name="status" class="form-select">
                                <option value="draft" {{ $product->status == 'draft' ? 'selected' : '' }}>Draft (Skriveno)</option>
                                <option value="published" {{ $product->status == 'published' ? 'selected' : '' }}>Objavljeno (Vidljivo)</option>
                                <option value="archive" {{ $product->status == 'archive' ? 'selected' : '' }}>Arhivirano</option>
                            </select>
                        </div>

                        <div class="card border-primary mb-3">
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" 
                                           {{ $product->is_featured ? 'checked' : '' }} value="1">
                                    <label class="form-check-label fw-bold" for="isFeatured">Istaknut proizvod</label>
                                </div>

                                <div id="featuredImageSection" class="mt-3 {{ $product->is_featured ? '' : 'd-none' }}">
                                    <label class="form-label small fw-bold text-primary">Izaberi naslovnu sliku iz galerije:</label>
                                    <div class="row g-2">
                                        @foreach($product->images as $img)
                                        <div class="col-4">
                                            <label class="d-block border p-1 rounded cursor-pointer">
                                                <input type="radio" name="main_image_path" value="{{ $img->image_path }}" 
                                                       {{ $product->image == $img->image_path ? 'checked' : '' }}>
                                                <img src="{{ asset($img->image_path) }}" class="img-fluid rounded">
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mb-4">
                    <label class="form-label fw-bold">Dodaj još slika u galeriju</label>
                    <input type="file" name="images[]" class="form-control" multiple>
                    
                    <div class="mt-3 row g-3">
                        <label class="d-block fw-bold mb-2">Trenutna galerija (Klikni na X za brisanje slike):</label>
                        @foreach($product->images as $img)
                 <div class="col-md-2 position-relative text-center image-container" id="image-{{ $img->id }}">
                  <img src="{{ asset($img->image_path) }}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
    
                 <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-image" data-id="{{ $img->id }}" title="Obriši sliku">
                   <i class="bi bi-x"></i>
                </button>
                 </div>
                @endforeach
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0">Tehničke Specifikacije (Kategorija: {{ $product->category->name }})</h6>
                    </div>
                    <div class="card-body">
                        @if($product->category->attribute_groups && count($product->category->attribute_groups) > 0)
                            
                            @foreach($product->category->attribute_groups as $groupName => $attributes)
                                <div class="group-section mb-4">
                                    <h7 class="fw-bold text-primary border-bottom d-block pb-1 mb-3">
                                        <i class="bi bi-gear-fill me-1"></i> {{ $groupName }}
                                    </h7>
                                    
                                    <div class="row">
                                        @foreach($attributes as $attr)
                                            @php
                                                $specKey = $groupName . '_' . $attr;
                                            @endphp
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small">{{ $attr }}</label>
                                                <input type="text" 
                                                       name="specs[{{ $specKey }}]" 
                                                       value="{{ $product->specs[$specKey] ?? ($product->specs[$attr] ?? '') }}" 
                                                       class="form-control form-control-sm" 
                                                       placeholder="Unesi {{ strtolower($attr) }}...">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                    
                        @elseif($product->category->attribute_names)
                            <div class="row">
                                @foreach($product->category->attribute_names as $attr)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ $attr }}</label>
                                        <input type="text" 
                                               name="specs[{{ $attr }}]" 
                                               value="{{ $product->specs[$attr] ?? '' }}" 
                                               class="form-control" 
                                               placeholder="Unesi {{ strtolower($attr) }}...">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted italic">Ova kategorija nema definisane specifične atribute.</p>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    <a href="{{ route('product.index') }}" class="btn btn-light border">Odustani</a>
                    <button type="submit" class="btn btn-dark px-4">Sačuvaj izmene</button>
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

function openSourceEditor() {
    const html = window.editor.getData();

    document.getElementById('htmlSource').value = html;

    sourceModal.show();
}

function applySource() {
    const html = document.getElementById('htmlSource').value;

    window.editor.setData(html);

    sourceModal.hide();
}
  </script>
<script>
    document.getElementById('newsForm').addEventListener('submit', function () {
    document.getElementById('content').value = window.editor.getData();
});
</script>

<script>
    
    document.getElementById('isFeatured').addEventListener('change', function() {
        const section = document.getElementById('featuredImageSection');
        if(this.checked) {
            section.classList.remove('d-none');
        } else {
            section.classList.add('d-none');
        }
    });
    document.querySelectorAll('.delete-image').forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.getAttribute('data-id');
            const container = document.getElementById(`image-${imageId}`);

            if (confirm('Da li ste sigurni da želite da obrišete ovu sliku?')) {
                fetch(`/admin/product-image/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        container.remove(); 
                    }
                })
                .catch(error => console.error('Greška:', error));
            }
        });
    });
</script>
@endsection