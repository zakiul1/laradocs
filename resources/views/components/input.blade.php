@props([
    'id' => null,
    'type' => 'text',
    'name' => null,
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'autocomplete' => null,
])

@php
    // Resolve field/id safely
    $field = $name ?? $attributes->get('name'); // may be null if caller forgot
    $idAttr = $id ?? (is_string($field) && $field !== '' ? $field : uniqid('input_'));

    // Only pull old input if we have a valid string field name
    $raw = is_string($field) && $field !== '' ? old($field, $value) : $value;

    // Ensure the value we render is a scalar string, never an array
    $valueAttr = is_array($raw) ? '' : (string) $raw;
@endphp

<input id="{{ $idAttr }}" @if ($field) name="{{ $field }}" @endif
    type="{{ $type }}" value="{{ $valueAttr }}" placeholder="{{ $placeholder }}"
    @if ($required) required @endif
    @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
    {{ $attributes->merge([
        'class' => 'bg-[#f2f8fd] text-gray-900 focus:border-[#c2e0f5] focus:bg-white autofill:bg-white
                                w-full rounded-lg focus:ring-0 border border-gray-300 px-4 py-2.5',
    ]) }} />

@if (is_string($field) && $field !== '')
    @error($field)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
@endif
