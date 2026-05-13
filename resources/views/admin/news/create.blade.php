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
                    <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data" id="newsForm">
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
                            <textarea name="summary" class="form-control" rows="2" placeholder="Kratka rečenica..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Glavni Sadržaj</label>
                            <div id="editor" style="min-height: 300px;"></div>
                            <textarea name="content" id="content" style="display: none;"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Glavna Slika Vesti</label>
                            <input type="file" name="image" class="form-control">
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


<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/super-build/ckeditor.js"></script>

<script>
    let editorInstance;

    function initializeEditor() {
        const availablePlugins = CKEDITOR.ClassicEditor.builtinPlugins.filter(plugin => {
            const name = plugin.pluginName;
            
            const forbidden = [
                'Collaboration', 'Comments', 'TrackChanges', 'Presence', 'Revision', 
                'CloudServices', 'RealTime', 'CKBox', 'CKFinder', 'EasyImage', 
                'Export', 'Import', 'AIAssistant', 'Adapter', 'CaseChange', 'Suggestions',
                
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

            return !forbidden.some(word => name.includes(word));
        });

        CKEDITOR.ClassicEditor
            .create(document.querySelector('#editor'), {
                plugins: availablePlugins,
                toolbar: {
                    items: [
                        'sourceEditing', '|', 
                        'heading', '|',
                        'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                        'outdent', 'indent', '|',
                        'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo'
                    ],
                    shouldNotGroupWhenFull: true
                },
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

    window.addEventListener('load', () => {
        if (typeof CKEDITOR !== 'undefined') {
            initializeEditor();
        } else {
            console.error('CKEditor biblioteka nije dostupna na CDN-u.');
        }
    });

    document.getElementById('newsForm').addEventListener('submit', function (e) {
        if (editorInstance) {
            const data = editorInstance.getData();
            document.getElementById('content').value = data;
        }
    });
</script>
@endsection