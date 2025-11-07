@props(['type' => 'button'])
<button type="{{ $type }}"
    {{ $attributes->merge(['class' => 'flex cursor-pointer items-center px-4 py-2 rounded-xl  bg-[#328cc5] text-white text-sm hover:bg-[#1d5987]']) }}>
    {{ $slot }}
</button>
