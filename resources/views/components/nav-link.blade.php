@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center justify-center h-7 px-3.5 pt-[1px] rounded-full text-xs font-semibold whitespace-nowrap bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 shadow-2xs transition-all duration-200'
            : 'inline-flex items-center justify-center h-7 px-3.5 pt-[1px] rounded-full text-xs font-medium whitespace-nowrap text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-200/60 dark:hover:bg-zinc-800/70 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
