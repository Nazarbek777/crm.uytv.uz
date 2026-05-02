@extends('layouts.main')

@section('title', 'Uyni tahrirlash')

@section('content')
@include('properties._form', [
    'action' => route('properties.update', $property),
    'method' => 'PUT',
    'submitLabel' => 'Yangilash',
])
@endsection
