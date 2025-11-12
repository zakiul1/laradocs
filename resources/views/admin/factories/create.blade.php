@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="mb-4 text-xl font-semibold text-gray-900">Add Factory</h1>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            @include('admin.factories._form', ['factory' => null])
        </div>
    </div>
@endsection
