@extends('layouts.admin')
@section('title', $service->exists ? 'Service : '.$service->name : 'Nouveau service')
@section('nav') @include('admin._nav') @endsection

@section('content')
<p style="margin-bottom:1rem"><a class="btn btn-ghost" href="{{ route('admin.services.index') }}">← Retour</a></p>

<div class="card" style="margin-bottom:1rem">
    <h3>Informations</h3>
    <form method="POST" enctype="multipart/form-data"
          action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}">
        @csrf
        @if ($service->exists) @method('PUT') @endif
        <div class="grid-2">
            <div class="form-group"><label>Nom</label>
                <input type="text" name="name" value="{{ old('name', $service->name) }}" required>
                @error('name') <p class="error">{{ $message }}</p> @enderror</div>
            <div class="form-group"><label>Phrase d'accroche</label>
                <input type="text" name="lead" value="{{ old('lead', $service->lead) }}">
                @error('lead') <p class="error">{{ $message }}</p> @enderror</div>
        </div>
        <div class="form-group"><label>Description</label>
            <textarea name="description" rows="4">{{ old('description', $service->description) }}</textarea></div>
        <div class="form-group"><label>Image (JPG/PNG/WEBP, 3 Mo)</label>
            @if ($service->image_url) <p><img src="{{ $service->image_url }}" alt="" style="height:80px;border-radius:8px"></p> @endif
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
            @error('image') <p class="error">{{ $message }}</p> @enderror</div>
        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active))> Visible sur le site</label>
        <button class="btn btn-primary">Enregistrer</button>
    </form>
</div>

@if ($service->exists)
<div class="card" style="margin-bottom:1rem">
    <h3>Champs du formulaire de demande</h3>
    <table class="table">
        <tr><th>Ordre</th><th>Libellé</th><th>Type</th><th>Obligatoire</th><th></th></tr>
        @forelse ($service->fields as $f)
        <tr>
            <td class="actions">
                <form method="POST" action="{{ route('admin.services.fields.move', [$service, $f, 'up']) }}">@csrf<button class="btn btn-ghost" @disabled($loop->first)>↑</button></form>
                <form method="POST" action="{{ route('admin.services.fields.move', [$service, $f, 'down']) }}">@csrf<button class="btn btn-ghost" @disabled($loop->last)>↓</button></form>
            </td>
            <td>{{ $f->label }}</td>
            <td>{{ $f->type->getLabel() }}</td>
            <td>{{ $f->is_required ? 'Oui' : 'Non' }}</td>
            <td>
                <details>
                    <summary>Modifier</summary>
                    <form method="POST" action="{{ route('admin.services.fields.update', [$service, $f]) }}" style="margin-top:.5rem">
                        @csrf @method('PUT')
                        <input type="text" name="label" value="{{ $f->label }}" required>
                        <select name="type" style="margin:.5rem 0">
                            @foreach ($types as $t) <option value="{{ $t->value }}" @selected($f->type === $t)>{{ $t->getLabel() }}</option> @endforeach
                        </select>
                        <textarea name="options" rows="3" placeholder="Choix, un par ligne (liste de choix uniquement)">{{ implode("\n", $f->options ?? []) }}</textarea>
                        <label class="check"><input type="checkbox" name="is_required" value="1" @checked($f->is_required)> Obligatoire</label>
                        <button class="btn btn-primary">Enregistrer</button>
                    </form>
                    <form method="POST" action="{{ route('admin.services.fields.destroy', [$service, $f]) }}" onsubmit="return confirm('Supprimer ce champ ?')" style="margin-top:.5rem">
                        @csrf @method('DELETE')<button class="btn btn-ghost">Supprimer</button>
                    </form>
                </details>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="muted">Aucun champ.</td></tr>
        @endforelse
    </table>

    <form method="POST" action="{{ route('admin.services.fields.store', $service) }}" style="margin-top:1rem">
        @csrf
        <div class="inline-form">
            <input type="text" name="label" placeholder="Libellé du champ" required>
            <select name="type">
                @foreach ($types as $t) <option value="{{ $t->value }}">{{ $t->getLabel() }}</option> @endforeach
            </select>
            <label class="check"><input type="checkbox" name="is_required" value="1"> Obligatoire</label>
        </div>
        <textarea name="options" rows="2" placeholder="Choix, un par ligne (liste de choix uniquement)" style="margin:.5rem 0"></textarea>
        <button class="btn btn-primary">Ajouter le champ</button>
    </form>
    @foreach (['label', 'type', 'options'] as $f) @error($f) <p class="error">{{ $message }}</p> @enderror @endforeach
</div>

<div class="card">
    <h3>Étapes types des projets</h3>
    <table class="table">
        <tr><th>Ordre</th><th>Étape</th><th>Poids</th><th></th></tr>
        @forelse ($service->stepTemplates as $s)
        <tr>
            <td class="actions">
                <form method="POST" action="{{ route('admin.services.steps.move', [$service, $s, 'up']) }}">@csrf<button class="btn btn-ghost" @disabled($loop->first)>↑</button></form>
                <form method="POST" action="{{ route('admin.services.steps.move', [$service, $s, 'down']) }}">@csrf<button class="btn btn-ghost" @disabled($loop->last)>↓</button></form>
            </td>
            <td>{{ $s->name }}</td>
            <td>{{ $s->weight }}</td>
            <td>
                <details>
                    <summary>Modifier</summary>
                    <form method="POST" action="{{ route('admin.services.steps.update', [$service, $s]) }}" class="inline-form" style="margin-top:.5rem">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $s->name }}" required>
                        <input type="number" name="weight" value="{{ $s->weight }}" min="1" max="100" style="width:90px" required>
                        <button class="btn btn-primary">OK</button>
                    </form>
                    <form method="POST" action="{{ route('admin.services.steps.destroy', [$service, $s]) }}" onsubmit="return confirm('Supprimer cette étape type ?')" style="margin-top:.5rem">
                        @csrf @method('DELETE')<button class="btn btn-ghost">Supprimer</button>
                    </form>
                </details>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="muted">Aucune étape type : les projets de ce service naîtront vides.</td></tr>
        @endforelse
    </table>

    @php $total = $service->stepTemplates->sum('weight'); @endphp
    <p class="muted">Total des poids : {{ $total }}.
        @if ($service->stepTemplates->isNotEmpty() && $total !== 100)
            <strong style="color:var(--c-danger)">Attention : l'avancement sera faussé, le total devrait faire 100.</strong>
        @endif
    </p>

    <form method="POST" action="{{ route('admin.services.steps.store', $service) }}" class="inline-form" style="margin-top:1rem">
        @csrf
        <input type="text" name="name" placeholder="Nouvelle étape" required>
        <input type="number" name="weight" placeholder="Poids" value="10" min="1" max="100" style="width:90px" required>
        <button class="btn btn-primary">Ajouter</button>
    </form>
    @foreach (['name', 'weight'] as $f) @error($f) <p class="error">{{ $message }}</p> @enderror @endforeach
</div>
@endif
@endsection