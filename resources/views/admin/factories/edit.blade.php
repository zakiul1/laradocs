@extends('layouts.app')

@section('content')
    <h2 class="text-xl font-semibold mb-4">Edit Employee</h2>
    @include('employees._form', [
        'action' => route('employees.update', $employee),
        'method' => 'PUT',
        'employee' => $employee,
    ])
@endsection
