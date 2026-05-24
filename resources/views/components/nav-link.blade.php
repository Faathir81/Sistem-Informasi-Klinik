@props(['active'])

@php
$classes = ($active ?? false)
            ? 'clinic-nav-link clinic-nav-link-active'
            : 'clinic-nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
