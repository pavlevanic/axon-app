@push('scripts')
<script>
function syncNewsFormContent() {
    const contentField = document.getElementById('content');
    if (contentField && window.editor) {
        contentField.value = window.editor.getData();
    }
    return true;
}

let sourceModal;

document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('sourceModal');
    if (modalEl) {
        sourceModal = new bootstrap.Modal(modalEl);
    }
});

function openSourceEditor() {
    if (!window.editor) return;

    document.getElementById('htmlSource').value = window.editor.getData();
    sourceModal.show();
}

function applySource() {
    if (!window.editor) return;

    window.editor.setData(document.getElementById('htmlSource').value);
    sourceModal.hide();
}
</script>
@endpush
