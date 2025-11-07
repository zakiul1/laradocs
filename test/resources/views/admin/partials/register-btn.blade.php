@if (auth()->user()->isSuperAdmin())
    <a href="{{ route('admin.users.create') }}"
        class="inline-flex items-center px-4 py-2 rounded-xl border bg-gray-900 text-white text-sm">
        Register Admin
    </a>
@endif
