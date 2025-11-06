@props(['title', 'subtitle' => null, 'action' => null])

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-gray-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($action)
        <div>{{ $action }}</div>
    @endif
</div>
