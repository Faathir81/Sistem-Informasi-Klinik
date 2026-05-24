@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-[#7ba891] bg-[#eef8f2] px-4 py-3 text-start text-sm font-bold text-[#14342f] transition'
            : 'block w-full border-l-4 border-transparent px-4 py-3 text-start text-sm font-semibold text-[#46665f] transition hover:border-[#b8cec3] hover:bg-[#f3faf6] hover:text-[#14342f]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
