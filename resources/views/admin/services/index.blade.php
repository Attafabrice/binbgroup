@extends('layouts.admin')
@section('title', 'Services')
@section('nav') @include('admin._nav') @endsection

@section('content')
@can('create', App\Models\Service::class)
    <p style="margin-bottom:1rem"><a class="btn btn-primary" href="{{ route('admin.services.create') }}">Nouveau service</a></p>
@endcan

<div class="card">
<table class="table">
    <tr><th>Ordre</th><th>Service</th><th>Statut</th><th>Demandes</th><th>Projets</th><th></th></tr>
    @forelse ($services as $s)
    <tr>
        <td class="actions">
            @can('update', $s)
            <form method="POST" action="{{ route('admin.services.move', [$s, 'up']) }}">@csrf<button class="btn btn-ghost" @disabled($loop->first)>↑</button></form>
            <form method="POST" action="{{ route('admin.services.move', [$s, 'down']) }}">@csrf<button class="btn btn-ghost" @disabled($loop->last)>↓</button></form>
            @endcan
        </td>
        <td>{{ $s->name }}</td>
        <td><span class="badge {{ $s->is_active ? 'badge-success' : 'badge-gray' }}">{{ $s->is_active ? 'Actif' : 'Inactif' }}</span></td>
        <td>{{ $s->quote_requests_count }}</td>
        <td>{{ $s->projects_count }}</td>
        <td class="actions">
            <a class="btn btn-ghost" href="{{ route('admin.services.edit', $s) }}">Modifier</a>
            @can('delete', $s)
            <form method="POST" action="{{ route('admin.services.destroy', $s) }}" onsubmit="return confirm('Supprimer ce service ?')">
                @csrf @method('DELETE')<button class="btn btn-ghost">Supprimer</button>
            </form>
            @endcan
        </td>
    </tr>
    @empty
    <tr><td colspan="6">Aucun service.</td></tr>
    @endforelse
</table>
</div>
@endsection