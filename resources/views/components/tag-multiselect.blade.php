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
        <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    {{-- Selected pills --}}
    <div class="flex flex-wrap gap-2 mb-2">
        <template x-for="id in selected" :key="id">
            <span
                class="flex items-center gap-1 px-3 py-1 text-sm rounded-full
                       bg-blue-100 text-blue-700
                       dark:bg-blue-900 dark:text-blue-200"
            >
                <span x-text="$refs['tag-' + id].textContent"></span>
                <button type="button" @click="toggle(id)">✕</button>
            </span>
        </template>
    </div>

    {{-- Search --}}
    <input
        type="text"
        x-model="query"
        placeholder="Search topics..."
        class="w-full px-4 py-2 mb-3 rounded-lg border
               border-gray-300 dark:border-gray-700
               dark:bg-gray-900 dark:text-white"
    />

    {{-- Options --}}
    <div class="max-h-48 overflow-y-auto border rounded-lg
                border-gray-200 dark:border-gray-700">

        @foreach($options as $option)
            <div
                x-show="query === '' || '{{ strtolower($option->name) }}'.includes(query.toLowerCase())"
                @click="toggle({{ $option->id }})"
                class="px-4 py-2 cursor-pointer flex justify-between
                       hover:bg-gray-100 dark:hover:bg-gray-800"
            >
                <span x-ref="tag-{{ $option->id }}">
                    {{ $option->name }}
                </span>

                <span x-show="selected.includes({{ $option->id }})">✔</span>
            </div>
        @endforeach
    </div>

    {{-- Hidden inputs --}}
    <template x-for="id in selected" :key="'input-' + id">
        <input type="hidden" name="tag_ids[]" :value="id">
    </template>

    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
        You can select up to {{ $max }} topics.
    </p>
</div>
