@props(['value'])

<label {{ $attributes->merge(['class' => 'clinic-label block']) }}>
    {{ $value ?? $slot }}
</label>
