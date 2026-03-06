<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        wire:ignore
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            init() {
                const existingEditor = tinymce.get('tiny-editor-{{ $getStatePath() }}');
                if (existingEditor) {
                    existingEditor.remove();
                }

                tinymce.init({
                    target: this.$refs.editor,
                    id: 'tiny-editor-{{ $getStatePath() }}',
                    plugins: 'image link lists table code media wordcount',
                    toolbar: 'undo redo | blocks | bold italic underline strikethrough | link image media | align | bullist numlist | blockquote table | code',
                    menubar: false,
                    branding: false,
                    promotion: false,
                    height: 500,
                    images_upload_url: '/admin/tinymce/upload',
                    automatic_uploads: true,
                    images_upload_handler: (blobInfo) => new Promise((resolve, reject) => {
                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());

                        fetch('/admin/tinymce/upload', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                            },
                            body: formData,
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Upload failed');
                            }
                            return response.json();
                        })
                        .then(result => {
                            resolve(result.location);
                        })
                        .catch(error => {
                            reject('Image upload failed: ' + error.message);
                        });
                    }),
                    setup: (editor) => {
                        editor.on('init', () => {
                            if (this.state) {
                                editor.setContent(this.state);
                            }
                        });
                        editor.on('change input undo redo', () => {
                            this.state = editor.getContent();
                        });

                        this.$watch('state', (newVal) => {
                            if (newVal !== editor.getContent()) {
                                editor.setContent(newVal || '');
                            }
                        });
                    },
                });
            },
        }"
    >
        <textarea x-ref="editor" class="hidden">{{ $getState() }}</textarea>
    </div>
</x-dynamic-component>
