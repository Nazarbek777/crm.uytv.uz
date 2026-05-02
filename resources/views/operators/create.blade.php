@extends('layouts.main')
@section('title', 'Yangi operator')
@section('content')
@include('operators._form', ['action' => route('operators.store'), 'method' => 'POST', 'submitLabel' => 'Saqlash'])
@endsection
