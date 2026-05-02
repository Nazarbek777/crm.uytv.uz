@extends('layouts.main')
@section('title', 'Yangi eslatma')
@section('content')
@include('reminders._form', ['action' => route('reminders.store'), 'method' => 'POST', 'submitLabel' => 'Saqlash'])
@endsection
