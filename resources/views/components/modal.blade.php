@props(['open' => false, 'title' => ''])
<div x-data="{ open: @js($open) }">
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
        aria-modal="true" role="dialog" aria-label="{{ $title }}">
        <div class="bg-white rounded-2xl shadow w-full max-w-lg">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="font-semibold">{{ $title }}</h2>
                <button @click="open=false" aria-label="Close">&times;</button>
            </div>
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
