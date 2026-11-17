@extends('layouts.admin')
@section('title', 'Projets')
@section('nav') @include('admin._nav') @endsection

@section('content')
<form method="GET" class="card inline-form" style="margin-bottom:1rem">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Référence ou titre">
    <select name="status">
        <option value="">Tous les statuts</option>
        @foreach ($statuses as $s)
            <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->getLabel() }}</option>
        @endforeach
    </select>
    <button class="btn btn-primary">Filtrer</button>
    <a class="btn btn-ghost" href="{{ route('admin.projects.index') }}">Réinitialiser</a>
</form>

<div class="card">
<table class="table">
    <tr><th>Référence</th><th>Titre</th><th>Client</th><th>Responsable</th><th>Avancement</th><th>Statut</th><th></th></tr>
    @forelse ($projects as $p)
    <tr>
        <td>{{ $p->reference }}</td>
        <td>{{ $p->title }}</td>
        <td>{{ $p->client?->display_name }}</td>
        <td>{{ $p->manager?->name ?? '—' }}</td>
        <td><div class="progress"><span style="width:{{ $p->progress }}%"></span></div> {{ $p->progress }} %</td>
        <td><x-status-badge :status="$p->status" /></td>
        <td><a class="btn btn-ghost" href="{{ route('admin.projects.show', $p) }}">Voir</a></td>
    </tr>
    @empty
    <tr><td colspan="7">Aucun projet.</td></tr>
    @endforelse
</table>
</div>
<div style="margin-top:1rem">{{ $projects->links() }}</div>
@endsection