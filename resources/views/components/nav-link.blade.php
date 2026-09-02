@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold leading-5 bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 shadow-2xs transition-all duration-200'
            : 'inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-medium leading-5 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800/80 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
