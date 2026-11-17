@extends('layouts.client')
@section('title', 'Mon espace')
@section('nav') @include('client._nav') @endsection

@section('content')
<div class="stats" style="margin-bottom:1rem">
    <div class="card stat"><strong>{{ $projects }}</strong><span class="muted">Projets en cours</span></div>
    <div class="card stat"><strong>{{ $requests }}</strong><span class="muted">Demandes en traitement</span></div>
    <div class="card stat"><strong>{{ $quotes }}</strong><span class="muted">Devis à consulter</span></div>
</div>
<a class="btn btn-primary" href="{{ route('client.requests.create') }}">Nouvelle demande de devis</a>
@endsection