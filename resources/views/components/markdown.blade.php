@props(['text'])

@php
use League\CommonMark\Environment\Environment;
use League\CommonMark\CommonMarkConverter;

// Create a simple environment
$environment = new Environment([
    'html_input' => 'allow',
    'allow_unsafe_links' => false,
]);

$converter = new CommonMarkConverter([], $environment);

// Convert Markdown → HTML
$html = $converter->convert($text)->getContent();
@endphp

<div class="prose dark:prose-invert max-w-none">
    {!! $html !!}
</div>
