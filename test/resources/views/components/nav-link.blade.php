@props(['href' => '#', 'active' => false])

@php
    $base = 'flex items-center gap-3 px-3 py-2 rounded-xl transition select-none';
    $state = $active ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $base . ' ' . $state]) }}
    :class="sidebarOpen ? '' : 'justify-center'">
    {{ $slot }}
</a>
