@props([
    'name' => 'body',
    'value' => '',
    'id' => 'markdown-editor',
    'autosaveId' => null
])

@pushOnce('styles')
    <style>
    .editor-toolbar {
        border: none !important;
        border-bottom: 1px solid var(--color-rule, #e5e7eb) !important;
        background-color: var(--color-paper, #ffffff) !important;
        padding: 0.5rem !important;
    }

    .CodeMirror {
        border: none !important;
        min-height: 450px !important;
        font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif !important;
        font-size: 1.1rem;
        line-height: 1.8;
        padding: 1rem !important;
        background-color: var(--color-paper, #ffffff) !important;
        color: var(--color-ink, #000000) !important;
    }

    .editor-statusbar {
        padding: 10px 15px !important;
        color: var(--color-muted, #6b7280) !important;
        border-top: 1px solid var(--color-rule, #e5e7eb) !important;
        background-color: var(--color-paper, #ffffff) !important;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        font-size: 10px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
    }

    .prose img {
        display: inline-block !important; 
        max-width: 48% !important; 
        max-height: 180px !important; 
        width: auto !important;
        height: auto !important;
        margin: 1rem 1% !important; 
        border-radius: 0.125rem;
        border: 1px solid var(--color-rule, #e5e7eb);
        box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);
        object-fit: contain;
        vertical-align: middle; 
    }
    
    .dark .editor-toolbar { border-bottom-color: #374151 !important; background-color: transparent !important; }
    .dark .editor-toolbar button, .dark .editor-toolbar a { color: #9ca3af !important; }
    .dark .editor-toolbar button:hover, .dark .editor-toolbar button.active { background-color: #374151 !important; color: #ffffff !important; }
    .dark .editor-toolbar i.separator { border-color: #4b5563 !important; }
    .dark .CodeMirror { background-color: transparent !important; color: #f3f4f6 !important; }
    .dark .CodeMirror-cursor { border-left-color: #f3f4f6 !important; }
    .dark .editor-statusbar { border-top-color: #374151 !important; background-color: transparent !important; }
    .dark .prose img { border-color: #374151; background-color: transparent; }

    @media (max-width: 640px) {
        .prose img { max-width: 100% !important; max-height: 250px !important; display: block !important; margin: 1.5rem auto !important; }
    }
    </style>
@endpushOnce

<div 
    x-data="setupMarkdownEditor('{{ $id }}', '{{ $id . $autosaveId }}')" 
    x-init="init()"
    class="mb-12 border border-rule bg-paper rounded-sm shadow-sm"
>
    <div class="flex items-center justify-between border-b border-rule px-6 py-4 bg-aged/30">
        <label class="font-mono text-[10px] tracking-widest uppercase text-muted font-bold">
            Discussion Content
        </label>

        <div class="flex items-center gap-6 font-mono text-[10px] uppercase tracking-widest">
            <button 
                type="button"
                @click="mode = 'write'"
                :class="mode === 'write' 
                    ? 'text-ink border-b border-ink pb-1 font-bold'
                    : 'text-muted hover:text-ink transition-colors pb-1 border-b border-transparent'"
            >
                Write
            </button>

            <button 
                type="button"
                @click="mode = 'preview'; renderPreview()"
                :class="mode === 'preview' 
                    ? 'text-ink border-b border-ink pb-1 font-bold'
                    : 'text-muted hover:text-ink transition-colors pb-1 border-b border-transparent'"
            >
                Preview
            </button>
        </div>
    </div>

    <div class="p-2">
        <div x-show="mode === 'write'">
            <textarea name="{{ $name }}" id="{{ $id }}">{{ $value }}</textarea>
        </div>

        <div 
            x-show="mode === 'preview'"
            class="prose prose-lg dark:prose-invert max-w-none p-6 font-serif text-ink leading-relaxed min-h-[450px]"
            x-ref="previewContainer"
            x-html="compiledMarkdown"
        ></div>

        @error($name)
            <p class="text-accent-warm font-mono text-[10px] uppercase tracking-widest mt-4 font-bold px-4 pb-4">{{ $message }}</p>
        @enderror
    </div>
</div>

@pushOnce('scripts')
    <script>
    function setupMarkdownEditor(editorId, autosaveId) {
        return {
            mode: 'write',
            editor: null,
            compiledMarkdown: '',

            init() {
                if (this.editor) return;

                let config = {
                    element: document.getElementById(editorId),
                    spellChecker: false,
                    autoDownloadFontAwesome: false,
                    status: ["lines", "words"],
                    placeholder: "Draft your scholarly post here...\n\n- Use Markdown for structure.\n- Use $...$ or $$...$$ for LaTeX mathematical equations.\n- Drag & drop or paste images directly into this area.",
                    
                    uploadImage: true,
                    imageUploadFunction: (file, onSuccess, onError) => {
                        const formData = new FormData();
                        formData.append('image', file);

                        fetch('{{ route("images.upload") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network error');
                            return response.json();
                        })
                        .then(data => {
                            if(data.url) {
                                onSuccess(data.url); 
                            } else {
                                onError(data.message || 'Error processing image.');
                            }
                        })
                        .catch(error => {
                            console.error('Upload Error:', error);
                            onError('Image upload failed. Ensure the file is under 5MB and is a valid image format.');
                        });
                    },

                    toolbar: [
                        "bold", "italic", "heading", "|",
                        "quote", "unordered-list", "ordered-list", "|",
                        "link", "upload-image", "table", "|", 
                        "preview", "side-by-side", "fullscreen", "|",
                        "guide"
                    ],
                };

                if (autosaveId) {
                    config.autosave = {
                        enabled: true,
                        uniqueId: autosaveId,
                        delay: 2000,
                    };
                }

                this.editor = new window.EasyMDE(config);
            },

            renderPreview() {
                const markdownText = this.editor.value();
                
                if (window.marked) {
                    this.compiledMarkdown = window.marked.parse(markdownText, { breaks: true });
                }

                this.$nextTick(() => {
                    if (window.renderMathInElement) {
                        window.renderMathInElement(this.$refs.previewContainer, {
                            delimiters: [
                                {left: "$$", right: "$$", display: true},
                                {left: "$", right: "$", display: false}
                            ],
                            throwOnError: false 
                        });
                    }

                    if (window.hljs) {
                        this.$refs.previewContainer.querySelectorAll('pre code').forEach((block) => {
                            window.hljs.highlightElement(block);
                        });
                    }
                });
            }
        }
    }
    </script>
@endpushOnce