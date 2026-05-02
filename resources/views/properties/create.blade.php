@extends('layouts.main')

@section('title', 'Yangi uy qo\'shish')

@section('content')
@include('properties._form', [
    'action' => route('properties.store'),
    'method' => 'POST',
    'submitLabel' => 'Saqlash',
])
@endsection
