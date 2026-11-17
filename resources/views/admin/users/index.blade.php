@extends('layouts.admin')
@section('title', 'Utilisateurs')
@section('nav') @include('admin._nav') @endsection

@section('content')
<div class="card">
<table class="table">
    <tr><th>Nom</th><th>Email</th><th>Rôle</th></tr>
    @foreach ($users->reject->isClient() as $u)
    <tr>
        <td>{{ $u->name }}</td>
        <td>{{ $u->email }}</td>
        <td>
            <form method="POST" action="{{ route('admin.users.update', $u) }}" class="inline-form">
                @csrf @method('PUT')
                <select name="role">
                    <option value="">— aucun —</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r }}" @selected($u->hasRole($r))>{{ $r }}</option>
                    @endforeach
                </select>
                <button class="btn btn-ghost">OK</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
</div>
@endsection