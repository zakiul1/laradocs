@extends('layouts.app')
@section('title', 'Edit Employee — Siatex Docs')
@section('crumb', 'Employees / Edit')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.employees.index') }}"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            Back
        </a>
    </div>

    <x-section-title :title="'Edit Employee'" />

    @if (session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif
    @if ($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <x-card stickyFooter :bodyClass="'p-0'">
        <form id="employee-edit-form" method="POST" action="{{ route('admin.employees.update', $employee) }}"
            enctype="multipart/form-data" class="p-4 md:p-6 space-y-8">
            @csrf @method('PUT')

            {{-- Shared form (edit mode) --}}
            @include('admin.employees.form', ['employee' => $employee, 'isEdit' => true])
        </form>

        {{-- Sticky footer --}}
        <x-slot:footer>
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.employees.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Back
                </a>
                <button type="submit" form="employee-edit-form"
                    class="px-5 py-2.5 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition cursor-pointer">
                    Save Changes
                </button>
            </div>
        </x-slot:footer>
    </x-card>
@endsection
