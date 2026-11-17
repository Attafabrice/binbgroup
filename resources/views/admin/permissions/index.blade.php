@extends('layouts.admin')
@section('title', 'Permissions')
@section('nav') @include('admin._nav') @endsection

@section('content')
<div class="card" style="margin-bottom:1rem">
    <form method="POST" action="{{ route('admin.permissions.store') }}" class="inline-form">
        @csrf
        <input type="text" name="name" placeholder="ex. send_quote" value="{{ old('name') }}" required>
        <button class="btn btn-primary">Créer</button>
    </form>
    @error('name') <p class="error">{{ $message }}</p> @enderror
</div>

@foreach ($groups as $resource => $perms)
    <div class="card" style="margin-bottom:1rem">
        <h3>{{ $resource }}</h3>
        <div class="tags">
            @foreach ($perms as $p)
                <form method="POST" action="{{ route('admin.permissions.destroy', $p) }}"
                      onsubmit="return confirm('Supprimer {{ $p->name }} ?')">
                    @csrf @method('DELETE')
                    <span class="badge">{{ $p->name }} <button class="x" title="Supprimer">×</button></span>
                </form>
            @endforeach
        </div>
    </div>
@endforeach
@endsection