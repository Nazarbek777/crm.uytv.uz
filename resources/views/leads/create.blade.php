@extends('layouts.main')
@section('title', 'Yangi lid')
@section('content')
@include('leads._form', ['action' => route('leads.store'), 'method' => 'POST', 'submitLabel' => 'Saqlash'])
@endsection
