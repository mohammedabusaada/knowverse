@props([
    'label' => null,
    'options' => [],
    'selected' => [],
    'max' => 5,
])

@php
    $selected = collect($selected)->map(fn ($id) => (int) $id)->toArray();
@endphp

<div
    x-data="{
        query: '',
        selected: {{ json_encode($selected) }},
        max: {{ $max }},
        toggle(id) {
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(i => i !== id)
            } else if (this.selected.length < this.max) {
                this.selected.push(id)
            }
        }
    }"
    class="mb-6"
>

    @if($label)
        <label class="block mb-2 text-sm font-serif font-bold text-ink">
            {{ $label }}
        </label>
    @endif

    {{-- Selected pills --}}
    <div class="flex flex-wrap gap-2 mb-3 min-h-[32px]">
        <template x-for="id in selected" :key="id">
            <span
                class="flex items-center gap-2 px-3 py-1 font-mono text-[11px] tracking-wider text-ink bg-aged border border-rule rounded-sm"
            >
                <span class="opacity-40 font-serif text-sm leading-none">§</span>
                <span x-text="$refs['tag-' + id].textContent.trim().toLowerCase()"></span>
                <button type="button" @click="toggle(id)" class="ml-1 text-muted hover:text-accent-warm transition-colors focus:outline-none">&times;</button>
            </span>
        </template>
        <template x-if="selected.length === 0">
            <span class="text-sm font-serif italic text-muted py-1">No disciplines selected yet.</span>
        </template>
    </div>

    {{-- Search Input --}}
    <div class="relative">
        <input
            type="text"
            x-model="query"
            placeholder="Search disciplines..."
            class="w-full px-4 py-2.5 mb-2 rounded-sm border border-rule bg-transparent text-ink font-serif text-sm
                   placeholder:text-muted placeholder:italic
                   focus:outline-none focus:ring-0 focus:border-ink transition-colors"
        />
    </div>

    {{-- Options List --}}
    <div class="max-h-48 overflow-y-auto border border-rule rounded-sm bg-paper shadow-sm">
        @foreach($options as $option)
            <div
                x-show="query === '' || '{{ strtolower($option->name) }}'.includes(query.toLowerCase())"
                @click="toggle({{ $option->id }})"
                class="px-4 py-2 cursor-pointer flex justify-between items-center text-sm font-serif border-b border-rule last:border-0 hover:bg-aged/50 transition-colors"
            >
                <span x-ref="tag-{{ $option->id }}" class="text-ink">
                    {{ $option->name }}
                </span>

                <span x-show="selected.includes({{ $option->id }})" class="text-accent font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
        @endforeach
    </div>

    {{-- Hidden inputs --}}
    <template x-for="id in selected" :key="'input-' + id">
        <input type="hidden" name="tag_ids[]" :value="id">
    </template>

    <p class="mt-3 font-mono text-[10px] uppercase tracking-[0.1em] text-muted">
        Select up to {{ $max }} disciplines.
    </p>
</div>