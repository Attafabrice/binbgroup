@extends('layouts.admin')
@section('title', 'Tableau de bord')

@section('nav') @include('admin._nav') @endsection

@section('content')
    <div class="card">
        <p>Connecté en tant que <strong>{{ auth()->user()->getRoleNames()->implode(', ') }}</strong>.</p>
    </div>
@endsection