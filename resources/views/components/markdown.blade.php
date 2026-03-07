@props(['text'])

<div class="prose dark:prose-invert max-w-none">
    {!! \Illuminate\Support\Str::markdown($text, ['html_input' => 'escape']) !!}
</div>