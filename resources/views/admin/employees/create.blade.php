@extends('layouts.app')
@section('title', 'Add Employee — Siatex Docs')
@section('crumb', 'Employees / Add')

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

    <x-section-title :title="'Add Employee'" />

    @if ($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <x-card stickyFooter :bodyClass="'p-0'">
        <form id="employee-create-form" method="POST" action="{{ route('admin.employees.store') }}"
            enctype="multipart/form-data" class="p-4 md:p-6 space-y-8">
            @csrf

            {{-- Shared form --}}
            @include('admin.employees.form', ['employee' => null, 'isEdit' => false])
        </form>

        {{-- Sticky Save --}}
        <x-slot:footer>
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.employees.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Back
                </a>
                <button type="submit" form="employee-create-form"
                    class="px-5 py-2.5 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition cursor-pointer">
                    Save Employee
                </button>
            </div>
        </x-slot:footer>
    </x-card>
@endsection
