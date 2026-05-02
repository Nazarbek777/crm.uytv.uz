@extends('layouts.main')
@section('title', 'Eslatmani tahrirlash')
@section('content')
@include('reminders._form', ['action' => route('reminders.update', $reminder), 'method' => 'PUT', 'submitLabel' => 'Yangilash'])
@endsection
