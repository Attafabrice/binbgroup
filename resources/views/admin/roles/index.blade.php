@extends('layouts.admin')
@section('title', 'Rôles')
@section('nav') @include('admin._nav') @endsection

@section('content')
<p style="margin-bottom:1rem"><a class="btn btn-primary" href="{{ route('admin.roles.create') }}">Nouveau rôle</a></p>
<div class="card">
<table class="table">
    <tr><th>Rôle</th><th>Permissions</th><th>Utilisateurs</th><th></th></tr>
    @foreach ($roles as $r)
    <tr>
        <td>{{ $r->name }}</td>
        <td>{{ $r->name === 'super_admin' ? 'Toutes' : $r->permissions_count }}</td>
        <td>{{ $r->users_count }}</td>
        <td class="actions">
            @if ($r->name !== 'super_admin')
                <a class="btn btn-ghost" href="{{ route('admin.roles.edit', $r) }}">Modifier</a>
            @endif
            @unless (in_array($r->name, $protected))
                <form method="POST" action="{{ route('admin.roles.destroy', $r) }}" onsubmit="return confirm('Supprimer ce rôle ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-ghost">Supprimer</button>
                </form>
            @endunless
        </td>
    </tr>
    @endforeach
</table>
</div>
@endsection