@extends('layouts.main')
@section('title', $lead->name . ' — tahrirlash')
@section('content')
@include('leads._form', ['action' => route('leads.update', $lead), 'method' => 'PUT', 'submitLabel' => 'Yangilash'])
@endsection
