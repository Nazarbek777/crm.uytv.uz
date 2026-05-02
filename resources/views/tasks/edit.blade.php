@extends('layouts.main')
@section('title', 'Taskni tahrirlash')
@section('content')
@include('tasks._form', ['action' => route('tasks.update', $task), 'method' => 'PUT', 'submitLabel' => 'Yangilash'])
@endsection
