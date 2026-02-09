@props(['tag' => null, 'label' => null])

@php
    // If a tag object is passed, get its name. Otherwise use the label string.
    $displayText = $tag ? $tag->name : $label;
@endphp

<span {{ $attributes->merge(['class' => 'px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-medium rounded-md border border-indigo-100 dark:border-indigo-800 transition-colors hover:bg-indigo-100 dark:hover:bg-indigo-900/50']) }}>
    #{{ $displayText }}
</span>