@props([
    'id' => 'password',
    'name' => 'password',
    'label' => 'Kata sandi',
    'autocomplete' => 'current-password',
    'placeholder' => 'Masukkan kata sandi',
    'required' => true,
])

<div>
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-semibold text-foreground">{{ $label }}</label>
    <div class="relative">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="password"
            @if ($required) required @endif
            autocomplete="{{ $autocomplete }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'block h-12 w-full rounded-2xl border border-input bg-white px-4 pr-12 text-sm text-ink outline-none placeholder:text-muted-foreground focus:border-brand focus:ring-4 focus:ring-brand/15']) }}
        >
        <button
            type="button"
            class="absolute inset-y-0 right-0 grid w-11 place-items-center rounded-r-2xl text-muted-foreground hover:text-foreground focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
            data-password-toggle="{{ $id }}"
            aria-controls="{{ $id }}"
            aria-pressed="false"
            aria-label="Tampilkan kata sandi"
        >
            <span data-icon="show"><x-hugeicon name="ViewIcon" :size="18" /></span>
            <span data-icon="hide" class="hidden"><x-hugeicon name="ViewOffSlashIcon" :size="18" /></span>
        </button>
    </div>
</div>
