@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border border-rule bg-paper text-ink px-4 py-3 rounded-md focus:border-ink focus:ring-ink']) }}>
