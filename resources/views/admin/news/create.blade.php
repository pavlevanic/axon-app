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
                            <div class="text-center">
                                <button type="button" class="btn btn-dark mb-2 text-center mt-1" onclick="openSourceEditor()">
                                    <>Source
                                </button>
                            </div>
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
          <button class="btn btn-dark" onclick="applySource()">Potvrdi</button>
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
@endsection