@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10 animate-[fadeUp_0.8s_ease_both]">

    {{-- Header --}}
    <nav class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted mb-8 flex items-center gap-2">
        <a href="{{ route('posts.show', $post) }}" class="hover:text-ink transition-colors border-b border-transparent hover:border-ink">Discussion</a>
        <span class="opacity-50">/</span>
        <span class="text-ink font-bold">Edit</span>
    </nav>

    <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data" 
          x-data="{ 
            imagePreview: null, 
            currentImage: '{{ $post->image ? asset('storage/' . $post->image) : '' }}',
            isDropping: false,
            removeImage: false,
            
            clearImage() {
                this.imagePreview = null;
                this.currentImage = '';
                this.removeImage = true;
                $refs.coverInput.value = '';
            },
            
            handleDrop(e) {
                this.isDropping = false;
                if (e.dataTransfer.files.length > 0) {
                    const file = e.dataTransfer.files[0];
                    if (file.type.startsWith('image/')) {
                        this.imagePreview = URL.createObjectURL(file);
                        this.currentImage = '';
                        this.removeImage = false;
                        $refs.coverInput.files = e.dataTransfer.files;
                    }
                }
            }
          }">
        
        @csrf
        @method('PUT')

        {{-- Signal to Controller: Boolean flag for image deletion --}}
        <input type="hidden" name="remove_image" :value="removeImage ? 1 : 0">

        {{-- Discussion Title --}}
        <div class="mb-8">
            <input 
                type="text" 
                name="title" 
                value="{{ old('title', $post->title) }}" 
                placeholder="Title of your discussion..." 
                required 
                class="w-full bg-transparent border-none text-4xl sm:text-5xl font-heading font-black text-ink placeholder:text-muted/30 focus:ring-0 p-0 transition-colors"
            />
            @error('title')
                <p class="text-accent-warm font-mono text-[10px] uppercase tracking-widest mt-3 font-bold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Topic Classification (Tags) --}}
        <div class="mb-10 border-b border-rule pb-8">
            <x-tag-multiselect
                :options="$tags"
                :selected="old('tag_ids', $post->tags->pluck('id')->toArray())"
                max="5" 
                placeholder="Select up to 5 topics..."
            />
        </div>

        {{-- Body (Markdown Editor) --}}
        <x-markdown-editor 
            name="body" 
            :value="old('body', $post->body)" 
        />

        {{-- Cover Image Attachment --}}
        <div class="mb-12 border-t border-rule pt-8 mt-12">
            <label class="block font-mono text-[10px] uppercase tracking-widest text-muted mb-4 font-bold">
                Cover Image (Optional)
            </label>
            
            <div 
                @dragover.prevent="isDropping = true"
                @dragleave.prevent="isDropping = false"
                @drop.prevent="handleDrop($event)"
                @click="$refs.coverInput.click()"
                :class="isDropping ? 'border-ink bg-aged/50' : (imagePreview || currentImage ? 'border-ink bg-aged/10' : 'border-rule bg-transparent hover:border-ink hover:bg-aged/10')"
                class="relative flex flex-col items-center justify-center w-full p-10 border-2 border-dashed rounded-sm transition-all cursor-pointer group"
            >
                <input 
                    x-ref="coverInput"
                    type="file" 
                    name="image" 
                    accept="image/*" 
                    class="hidden" 
                    @change="imagePreview = URL.createObjectURL($event.target.files[0]); currentImage = ''; removeImage = false;" 
                />

                {{-- Empty State --}}
                <div x-show="!imagePreview && !currentImage" class="text-center">
                    <svg class="mx-auto h-10 w-10 text-muted group-hover:text-ink transition-colors mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2-2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="font-serif text-sm text-ink mb-1">Click to select or drag an image here</p>
                    <p class="font-mono text-[9px] uppercase tracking-widest text-muted">PNG, JPG, WEBP (Max 5MB)</p>
                </div>

                {{-- Preview State --}}
                <div x-show="imagePreview || currentImage" style="display: none;" class="w-full text-center relative">
                    <img :src="imagePreview || currentImage" class="max-h-64 mx-auto rounded-sm shadow-sm border border-rule object-contain">
                    
                    {{-- Discard/Remove Button --}}
                    <button type="button" @click.stop="clearImage()" class="absolute -top-4 -right-4 bg-accent-warm text-paper p-2 rounded-full shadow-lg hover:opacity-80 transition-opacity z-10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>

                    <div class="mt-6 inline-flex items-center justify-center px-5 py-2 border border-rule bg-paper text-[10px] font-mono uppercase tracking-widest text-muted group-hover:text-ink group-hover:border-ink transition-colors">
                        Click or drop to replace
                    </div>
                </div>
            </div>
            
            @error('image')
                <p class="text-accent-warm font-mono text-[10px] uppercase tracking-widest mt-3 font-bold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-6 pt-6 border-t border-rule mt-8">
            <a href="{{ route('posts.show', $post) }}" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink transition-colors">
                Cancel
            </a>
            <button type="submit" class="bg-ink text-paper font-mono text-[10px] uppercase tracking-widest px-8 py-3 hover:bg-transparent hover:text-ink border border-ink transition-all shadow-sm">
                Update Discussion
            </button>
        </div>
    </form>

    {{-- Danger Zone --}}
    @can('delete', $post)
        <div class="border-t border-rule pt-8 mt-16">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-accent-warm/5 border border-accent-warm/20">
                <div>
                    <h3 class="font-heading font-bold text-accent-warm text-lg">Danger Zone</h3>
                    <p class="font-serif text-[15px] text-accent-warm/80 mt-1 italic">Permanently delete this discussion and all associated responses.</p>
                </div>
                <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('WARNING: This action is irreversible. Proceed with deletion?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto bg-accent-warm text-paper font-mono text-[10px] uppercase tracking-widest px-6 py-3 hover:opacity-80 transition-opacity shadow-sm">
                        Delete Discussion
                    </button>
                </form>
            </div>
        </div>
    @endcan

</div>
@endsection