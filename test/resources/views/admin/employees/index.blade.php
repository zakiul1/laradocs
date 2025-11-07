@extends('layouts.app')
@section('title', 'Employees — Siatex Docs')
@section('crumb', 'Employees')

@section('content')
    <x-section-title :title="'Employees'" :subtitle="'Manage employee records'">
        <x-slot name="action">
            @include('admin.employees.partials.create-btn')
        </x-slot>
    </x-section-title>




    <x-card class="overflow-x-auto -mx-4 md:mx-0">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="p-3">Name</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Designation</th>
                    <th class="p-3">Join</th>
                    <th class="p-3">Leave</th>
                    <th class="p-3">Docs</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($employees as $e)
                    <tr>
                        <td class="p-3 flex items-center gap-3">
                            <img src="{{ $e->photoUrl() ?: 'https://placehold.co/40x40?text=E' }}"
                                class="w-10 h-10 rounded-full object-cover" alt="">
                            <div>
                                <div class="font-medium">{{ $e->name }}</div>
                                <div class="text-xs text-gray-500">{{ $e->address }}</div>
                            </div>
                        </td>
                        <td class="p-3">{{ $e->phone }}</td>
                        <td class="p-3">{{ $e->email }}</td>
                        <td class="p-3">{{ $e->designation }}</td>

                        <td class="p-3">{{ optional($e->join_date)->format('Y-m-d') }}</td>
                        <td class="p-3">{{ optional($e->leave_date)->format('Y-m-d') }}</td>
                        <td class="p-3">{{ $e->documents()->count() }}</td>
                        <td class="p-3 flex gap-2">
                            <a class="underline text-sm" href="{{ route('admin.employees.edit', $e) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.employees.destroy', $e) }}"
                                onsubmit="return confirm('Delete employee?')">
                                @csrf @method('DELETE')
                                <button class="underline text-sm text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-6 text-center text-gray-500" colspan="7">No employees yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $employees->links() }}</div>
    </x-card>
@endsection
