@extends('layouts.admin')
@section('title', $role->exists ? 'Rôle : '.$role->name : 'Nouveau rôle')
@section('nav') @include('admin._nav') @endsection

@section('content')
<form method="POST" class="card"
      action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
    @csrf
    @if ($role->exists) @method('PUT') @endif

    @unless ($role->exists)
        <div class="form-group">
            <label>Nom du rôle (minuscules et _)</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name') <p class="error">{{ $message }}</p> @enderror
        </div>
    @endunless

    @forelse ($groups as $resource => $perms)
        <fieldset class="perm-group">
            <legend>{{ $resource }}</legend>
            @foreach ($perms as $p)
                <label class="check">
                    <input type="checkbox" name="permissions[]" value="{{ $p->name }}"
                        @checked(in_array($p->name, old('permissions', $granted)))>
                    {{ $p->name }}
                </label>
            @endforeach
        </fieldset>
    @empty
        <p>Aucune permission. Crée-les d'abord dans « Permissions ».</p>
    @endforelse

    <button class="btn btn-primary">Enregistrer</button>
    <a class="btn btn-ghost" href="{{ route('admin.roles.index') }}">Annuler</a>
</form>
@endsection