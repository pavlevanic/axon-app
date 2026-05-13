@extends('layouts.admin')

@section('admin_content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-pencil-square me-2"></i>Izmeni Vest</span>
                    <span class="badge bg-secondary small">ID: #{{ $news->id }}</span>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('news.update', $news->id) }}" method="POST" enctype="multipart/form-data" id="newsForm">
                        @csrf
                        @method('PUT')

                        {{-- Naslov --}}
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Naslov Vesti</label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   value="{{ old('title', $news->title) }}" required>
                        </div>

                        {{-- Tip Vesti --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Gde će se prikazati?</label>
                            <select name="type" class="form-select border-primary text-primary fw-bold">
                                <option value="normal" {{ $news->type == 'normal' ? 'selected' : '' }}>Obična vest (Lista)</option>
                                <option value="hero" {{ $news->type == 'hero' ? 'selected' : '' }}>Hero Sekcija (Glavni Baner)</option>
                                <option value="promo" {{ $news->type == 'promo' ? 'selected' : '' }}>Promo (Manja kartica)</option>
                            </select>
                            <small class="text-muted d-block mt-1">Trenutno rezervisano za AXON strateške objave.</small>
                        </div>

                        <div class="mb-3 border rounded p-3 bg-light-subtle">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                                <label class="form-check-label fw-bold" for="isActive">
                                    <i class="bi bi-eye-fill me-1 text-success"></i> Status: Vest je aktivna
                                </label>
                            </div>
                            <small class="text-muted">Ako isključite ovo, vest se neće videti na sajtu, ali će ostati u bazi (Draft).</small>
                        </div>

                        {{-- Kratak opis --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kratak opis (Summary)</label>
                            <textarea name="summary" class="form-control" rows="2">{{ old('summary', $news->summary) }}</textarea>
                        </div>

                        {{-- Glavni tekst --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Glavni Sadržaj</label>
                            <div id="editor" style="min-height: 300px;">
                                {!! old('content', $news->content) !!}
                            </div>
                            <textarea name="content" id="content" style="display: none;"></textarea>
                        </div>

                        {{-- Slika --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Slika Vesti</label>
                            @if($news->image)
                                <div class="mb-2">
                                    <img src="{{ asset($news->image) }}" alt="Trenutna slika" 
                                         class="rounded shadow-sm border" style="max-height: 150px;">
                                    <p class="small text-muted mt-1">Trenutna slika (ostavite prazno ako ne želite promenu)</p>
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('news.index') }}" class="btn btn-light px-4 border">Odustani</a>
                            <button type="submit" class="btn btn-dark px-5 shadow">Sačuvaj Izmene</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/super-build/ckeditor.js"></script>

<script>
    let editorInstance;

    function initializeEditor() {
        // 1. Filtriranje pluginova - izbacujemo sve što traži licencu ili Cloud (AXON optimizacija)
        const availablePlugins = CKEDITOR.ClassicEditor.builtinPlugins.filter(plugin => {
            const name = plugin.pluginName;
            
            const forbidden = [
                'Collaboration', 'Comments', 'TrackChanges', 'Presence', 'Revision', 
                'CloudServices', 'RealTime', 'CKBox', 'CKFinder', 'EasyImage', 
                'Export', 'Import', 'AIAssistant', 'Adapter', 'CaseChange', 'Suggestions',
                
                // Komercijalni pluginovi koji blokiraju rad bez licence
                'FormatPainter', 
                'Template', 
                'SlashCommand', 
                'PasteFromOfficeEnhanced', 
                'DocumentOutline', 
                'TableOfContents', 
                'Pagination', 
                'WProofreader', 
                'MathType', 
                'ChemType'
            ];

            // Vraća true samo ako plugin nije na listi zabranjenih
            return !forbidden.some(word => name.includes(word));
        });

        // 2. Inicijalizacija editora
        CKEDITOR.ClassicEditor
            .create(document.querySelector('#editor'), {
                plugins: availablePlugins,
                toolbar: {
                    items: [
                        'sourceEditing', '|', // Tvoje dugme za HTML kod
                        'heading', '|',
                        'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                        'outdent', 'indent', '|',
                        'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo'
                    ],
                    shouldNotGroupWhenFull: true
                },
                // Isključujemo potrebu za licencnim ključem
                licenseKey: '', 
                htmlSupport: {
                    allow: [{
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }]
                },
                simpleUpload: {
                    uploadUrl: "{{ route('news.upload.image') }}",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                },
                language: 'sr'
            })
            .then(editor => {
                editorInstance = editor;
                console.log('AXON: Editor je spreman i Source Editing je aktivan!');
            })
            .catch(error => {
                console.error('Kritična greška pri inicijalizaciji:', error);
            });
    }

    // 3. Pokretanje nakon što se sve učita
    window.addEventListener('load', () => {
        if (typeof CKEDITOR !== 'undefined') {
            initializeEditor();
        } else {
            console.error('CKEditor biblioteka nije dostupna na CDN-u.');
        }
    });

    // 4. Sinhronizacija pre slanja forme (kopira HTML iz editora u skriveni textarea)
    document.getElementById('newsForm').addEventListener('submit', function (e) {
        if (editorInstance) {
            const data = editorInstance.getData();
            document.getElementById('content').value = data;
        }
    });
</script>
@endsection