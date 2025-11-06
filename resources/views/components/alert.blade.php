@props(['type' => 'info', 'message' => null])
@php
    $classes = match ($type) {
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'error' => 'bg-red-50 border-red-200 text-red-800',
        default => 'bg-blue-50 border-blue-200 text-blue-800',
    };
@endphp
<div class="rounded-xl border px-4 py-3 mb-4 {{ $classes }}">
    {{ $message ?? $slot }}
</div>
