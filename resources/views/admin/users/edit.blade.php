@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Edit User</h1>
        <a href="{{ route('admin.users.index') }}" class="text-sm underline">Back</a>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <x-label for="name" value="Name" />
                <x-input name="name" required :value="$user->name" />
            </div>

            <div>
                <x-label for="email" value="Email" />
                <x-input name="email" type="email" required :value="$user->email" />
            </div>

            <div>
                <x-label for="role" value="Role" />
                <select name="role" id="role"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-0">
                    <option value="admin" @selected($user->role === 'admin')>admin</option>
                    <option value="super_admin" @selected($user->role === 'super_admin')>super_admin</option>
                </select>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked($user->is_active)
                    class="rounded border-gray-300">
                Active
            </label>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-label for="password" value="New Password (optional)" />
                    <x-input name="password" type="password" />
                </div>
                <div>
                    <x-label for="password_confirmation" value="Confirm New Password" />
                    <x-input name="password_confirmation" type="password" />
                </div>
            </div>

            <x-button type="submit">Save changes</x-button>
        </form>
    </x-card>
@endsection
