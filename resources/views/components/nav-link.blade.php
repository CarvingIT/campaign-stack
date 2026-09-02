@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold leading-5 bg-[#FFC700] text-[#0B192C] shadow-xs transition-all duration-200'
            : 'inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold leading-5 text-gray-600 dark:text-gray-300 hover:text-[#0B192C] dark:hover:text-white hover:bg-gray-100/80 dark:hover:bg-[#1E3E62]/50 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
