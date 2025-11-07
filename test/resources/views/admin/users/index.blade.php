@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Users</h1>
            <p class="text-sm text-gray-500">Manage admins and super admins</p>
        </div>
        @if (auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center px-4 py-2 rounded-xl border bg-gray-900 text-white text-sm">
                Register Admin
            </a>
        @endif
    </div>

    <x-card>
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-3">Name</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Created</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($users as $user)
                        <tr>
                            <td class="p-3">{{ $user->name }}</td>
                            <td class="p-3">{{ $user->email }}</td>
                            <td class="p-3">{{ $user->role }}</td>
                            <td class="p-3">
                                @if ($user->is_active)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Inactive</span>
                                @endif
                            </td>
                            <td class="p-3">{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="p-3 flex gap-2">
                                @if (auth()->user()->isSuperAdmin())
                                    <a href="{{ route('admin.users.edit', $user) }}" class="underline text-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                        onsubmit="return confirm('Deactivate user?')">
                                        @csrf @method('DELETE')
                                        <button class="underline text-sm text-red-600">Deactivate</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </x-card>
@endsection
