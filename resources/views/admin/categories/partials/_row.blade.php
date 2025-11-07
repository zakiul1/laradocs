@php($pad = str_repeat('—', $depth))
<tr>
    <td class="p-3">
        <span class="font-medium">{{ $pad }} {{ $cat->name }}</span>
    </td>
    <td class="p-3">{{ optional($cat->parent)->name ?: '—' }}</td>
    <td class="p-3">
        <details class="inline-block">
            <summary class="cursor-pointer underline">Quick edit</summary>
            <form method="POST" action="{{ route('admin.categories.update', $cat) }}" class="mt-2 space-x-2 inline-flex">
                @csrf @method('PUT')
                <input type="text" name="name" value="{{ $cat->name }}"
                    class="rounded-md border px-2 py-1 text-sm">
                <select name="parent_id" class="rounded-md border px-2 py-1 text-sm">
                    <option value="">None</option>
                    @foreach (\App\Models\Category::flatForSelect($cat->scope) as $id => $label)
                        @if ($id !== $cat->id)
                            <option value="{{ $id }}" @selected($id == $cat->parent_id)>{{ $label }}
                            </option>
                        @endif
                    @endforeach
                </select>
                <button class="px-3 py-1 rounded-md border bg-white hover:bg-gray-50 text-sm">Save</button>
            </form>
            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="inline"
                onsubmit="return confirm('Delete this category?')">
                @csrf @method('DELETE')
                <button
                    class="px-3 py-1 rounded-md border bg-white hover:bg-gray-50 text-sm text-red-600">Delete</button>
            </form>
        </details>
    </td>
</tr>

{{-- children --}}
@foreach ($cat->children as $child)
    @include('admin.categories.partials._row', ['cat' => $child, 'depth' => $depth + 1])
@endforeach
