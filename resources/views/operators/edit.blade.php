@extends('layouts.main')
@section('title', $operator->name . ' — tahrirlash')
@section('content')
@include('operators._form', ['action' => route('operators.update', $operator), 'method' => 'PUT', 'submitLabel' => 'Yangilash'])
@endsection
