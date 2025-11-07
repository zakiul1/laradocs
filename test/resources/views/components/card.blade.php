@props([
    'title' => null,
    'action' => null,
    'footer' => null, // keep prop support if you used it elsewhere
    'bodyClass' => null,
    'stickyHeader' => false,
    'stickyFooter' => false,
    'scrollBody' => false,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border bg-white shadow-sm']) }}>
    @if ($title || $action)
        <div class="{{ $stickyHeader ? 'sticky top-0 z-10' : '' }} px-6 py-4 border-b bg-white/90 backdrop-blur">
            <div class="flex items-center justify-between gap-4">
                @if ($title)
                    <h2 class="text-base font-semibold">{{ $title }}</h2>
                @endif
                @if ($action)
                    <div>{{ $action }}</div>
                @endif
            </div>
        </div>
    @endif

    <div class="{{ $bodyClass ?? 'p-6' }} @if ($scrollBody) overflow-y-auto @endif">
        {{ $slot }}
    </div>

    {{-- Prefer named slot; fall back to prop if provided --}}
    @isset($footer)
        <div class="{{ $stickyFooter ? 'sticky bottom-0 z-10' : '' }} px-6 py-4 border-t bg-white/90 backdrop-blur">
            {{ $footer }}
        </div>
    @elseif (!empty($footer))
        <div class="{{ $stickyFooter ? 'sticky bottom-0 z-10' : '' }} px-6 py-4 border-t bg-white/90 backdrop-blur">
            {!! $footer !!}
        </div>
    @endisset
</div>
