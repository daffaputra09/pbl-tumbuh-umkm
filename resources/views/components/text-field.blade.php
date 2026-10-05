@props([
    'id',
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'autocomplete' => 'on',
    'placeholder' => '',
])

<div>
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-semibold text-foreground">{{ $label }}</label>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $value }}"
        required
        autocomplete="{{ $autocomplete }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'block h-12 w-full rounded-2xl border border-input bg-white px-4 text-sm text-ink outline-none placeholder:text-muted-foreground focus:border-brand focus:ring-4 focus:ring-brand/15']) }}
    >
</div>
