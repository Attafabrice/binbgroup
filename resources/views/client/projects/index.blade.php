@extends('layouts.client')
@section('title', 'Mes projets')

@section('content')
<div class="card">
<table class="table">
    <tr><th>Référence</th><th>Projet</th><th>Avancement</th><th>Statut</th><th></th></tr>
    @forelse ($projects as $p)
    <tr>
        <td>{{ $p->reference }}</td>
        <td>{{ $p->title }}</td>
        <td><div class="progress"><span style="width:{{ $p->progress }}%"></span></div> {{ $p->progress }} %</td>
        <td><x-status-badge :status="$p->status" /></td>
        <td><a class="btn btn-ghost" href="{{ route('client.projects.show', $p) }}">Suivre</a></td>
    </tr>
    @empty
    <tr><td colspan="5" class="muted">Aucun projet pour le moment. Il sera créé dès l'acceptation d'un devis.</td></tr>
    @endforelse
</table>
</div>
<div style="margin-top:1rem">{{ $projects->links() }}</div>
@endsection