@extends('layouts.main')
@section('title', 'Yangi task')
@section('content')
@include('tasks._form', ['action' => route('tasks.store'), 'method' => 'POST', 'submitLabel' => 'Saqlash'])
@endsection
