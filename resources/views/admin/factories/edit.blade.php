@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h2 class="text-xl font-semibold mb-4">Edit Factory</h2>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            @include('admin.factories._form', [
                'action' => route('admin.factories.update', $factory->id),
                'method' => 'PUT',
                'factory' => $factory,
            ])
        </div>
    </div>
@endsection
