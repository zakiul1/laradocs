@props([
    'id' => null,
    'type' => 'text',
    'name',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'autocomplete' => null,
])
<input id="{{ $id ?? $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}" @if ($required) required @endif
    @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
    {{ $attributes->merge(['class' => 'bg-[#f2f8fd] text-gray-900 focus:border-[#c2e0f5] focus:bg-white autofill:bg-white w-full rounded-lg  focus:ring-0']) }} />
@error($name)
    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
@enderror
