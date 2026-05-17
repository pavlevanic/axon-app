import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

class MyUploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return this.loader.file.then(file => {
            return new Promise((resolve, reject) => {

                const data = new FormData();
                data.append('upload', file);

                fetch('/news/upload-image', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: data
                })
                .then(res => res.json())
                .then(res => resolve({ default: res.url }))
                .catch(reject);
            });
        });
    }

    abort() {}
}

function UploadAdapterPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = loader => {
        return new MyUploadAdapter(loader);
    };
}

export function initEditor() {

    const el = document.querySelector('#editor');
    if (!el) return;

    ClassicEditor.create(el, {
        toolbar: [
            'heading',
            '|',
            'bold',
            'italic',
            'link',
            'bulletedList',
            'numberedList',
            '|',
            'imageUpload',
            'blockQuote',
            'insertTable',
            'undo',
            'redo'
        ],

        extraPlugins: [UploadAdapterPlugin]

    }).then(editor => {

        window.editor = editor;

        // 🔥 SOURCE (HTML VIEW) ALTERNATIVA
        window.toggleSource = function () {
            const current = editor.getData();

            const modal = document.createElement('textarea');
            modal.style.width = '100%';
            modal.style.height = '400px';
            modal.value = current;

            const ok = confirm("Edit HTML content? OK = open editor");

            if (ok) {
                const newHtml = prompt("Edit HTML:", current);
                if (newHtml !== null) {
                    editor.setData(newHtml);
                }
            }
        };

    }).catch(console.error);
}