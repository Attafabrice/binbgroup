@extends('layouts.admin')

@section('title', 'Projet '.$p->reference)
@section('nav') @include('admin._nav') @endsection

@section('content')
    {{-- ========================================================= --}}
    {{-- En-tête du projet --}}
    {{-- ========================================================= --}}
    <div class="card" style="margin-bottom: 1rem;">
        <div class="actions" style="justify-content: space-between;">
            <div>
                <x-status-badge :status="$p->status" />
                <strong>{{ $p->client?->display_name }}</strong> · {{ $p->service?->name }}
                @if ($p->quote)
                    · devis <a href="{{ route('admin.quotes.show', $p->quote) }}">{{ $p->quote->reference }}</a> ({{ \App\Models\Quote::money($p->quote->total) }} TTC)
                @endif
            </div>
            <a class="btn btn-ghost" href="{{ route('admin.projects.index') }}">← Retour</a>
        </div>
        <div class="progress" style="margin-top: .75rem;">
            <span style="width: {{ $p->progress }}%"></span>
        </div>
        <p class="muted">Avancement : {{ $p->progress }} %</p>
    </div>

    {{-- ========================================================= --}}
    {{-- Informations --}}
    {{-- ========================================================= --}}
    <div class="card" style="margin-bottom: 1rem;">
        <h3>Informations</h3>
        @can('update', $p)
            <form method="POST" action="{{ route('admin.projects.update', $p) }}">
                @csrf 
                @method('PUT')
                <div class="grid-2">
                    <div class="form-group">
                        <label>Titre</label>
                        <input type="text" name="title" value="{{ old('title', $p->title) }}" required>
                        @error('title') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>Lieu</label>
                        <input type="text" name="location" value="{{ old('location', $p->location) }}">
                    </div>
                    <div class="form-group">
                        <label>Statut</label>
                        <select name="status">
                            @foreach ($statuses as $s)
                                <option value="{{ $s->value }}" @selected(old('status', $p->status->value) === $s->value)>{{ $s->getLabel() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Responsable</label>
                        <select name="manager_id">
                            <option value="">— aucun —</option>
                            @foreach ($managers as $m)
                                <option value="{{ $m->id }}" @selected((int) old('manager_id', $p->manager_id) === $m->id)>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Début</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $p->start_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label>Fin prévue</label>
                        <input type="date" name="planned_end_date" value="{{ old('planned_end_date', $p->planned_end_date?->format('Y-m-d')) }}">
                        @error('planned_end_date') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>Fin réelle</label>
                        <input type="date" name="actual_end_date" value="{{ old('actual_end_date', $p->actual_end_date?->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3">{{ old('description', $p->description) }}</textarea>
                </div>
                <button class="btn btn-primary">Enregistrer</button>
            </form>
        @else
            <p>{{ $p->description ?: 'Aucune description.' }}</p>
        @endcan
    </div>

    {{-- ========================================================= --}}
    {{-- Étapes --}}
    {{-- ========================================================= --}}
    <div class="card" style="margin-bottom: 1rem;">
        <h3>Étapes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Étape</th>
                    <th>Poids</th>
                    <th>Statut</th>
                    <th>Date prévue</th>
                    <th>Visible client</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($p->steps as $s)
                    @php $done = $s->status === \App\Enums\StepStatus::Termine; @endphp
                    <tr @class(['step-done' => $done])>
                        @can('manageSteps', $p)
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->weight }}</td>
                            @if ($done)
                                <td><x-status-badge :status="$s->status" /></td>
                                <td>{{ $s->planned_date?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $s->is_visible_to_client ? 'Oui' : 'Non' }}</td>
                                <td class="muted">Terminée le {{ $s->completed_at?->format('d/m/Y') }}</td>
                            @else
                                <form method="POST" action="{{ route('admin.projects.steps.update', [$p, $s]) }}" id="step-{{ $s->id }}">
                                    @csrf 
                                    @method('PUT')
                                </form>
                                <td>
                                    <select name="status" form="step-{{ $s->id }}">
                                        @foreach ($stepStatuses as $ss)
                                            <option value="{{ $ss->value }}" @selected($s->status === $ss)>{{ $ss->getLabel() }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="date" name="planned_date" form="step-{{ $s->id }}" value="{{ $s->planned_date?->format('Y-m-d') }}">
                                </td>
                                <td>
                                    <input type="checkbox" name="is_visible_to_client" value="1" form="step-{{ $s->id }}" @checked($s->is_visible_to_client)>
                                </td>
                                <td class="actions">
                                    <button class="btn btn-ghost" form="step-{{ $s->id }}">OK</button>
                                    <form method="POST" action="{{ route('admin.projects.steps.destroy', [$p, $s]) }}" onsubmit="return confirm('Supprimer cette étape ?')">
                                        @csrf 
                                        @method('DELETE')
                                        <button class="btn btn-ghost">×</button>
                                    </form>
                                </td>
                            @endif
                        @else
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->weight }}</td>
                            <td>{{ $s->status->getLabel() }}</td>
                            <td>{{ $s->planned_date?->format('d/m/Y') }}</td>
                            <td>{{ $s->is_visible_to_client ? 'Oui' : 'Non' }}</td>
                            <td></td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">Aucune étape.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <p class="muted">Total des poids : {{ $p->steps->sum('weight') }} (idéalement 100).</p>

        @can('manageSteps', $p)
            <form method="POST" action="{{ route('admin.projects.steps.store', $p) }}" class="inline-form" style="margin-top: 1rem;">
                @csrf
                <input type="text" name="name" placeholder="Nouvelle étape" required>
                <input type="number" name="weight" placeholder="Poids" value="10" min="1" max="100" style="width: 90px;" required>
                <button class="btn btn-primary">Ajouter</button>
            </form>
            @foreach (['name', 'weight'] as $f) 
                @error($f) <p class="error">{{ $message }}</p> @enderror 
            @endforeach
        @endcan
    </div>

    {{-- ========================================================= --}}
    {{-- Journal du chantier --}}
    {{-- ========================================================= --}}
    <div class="card" style="margin-top: 1rem;">
        <h3>Journal du chantier</h3>

        @can('publishUpdate', $p)
            <form method="POST" action="{{ route('admin.projects.journal.store', $p) }}" style="margin-bottom: 1rem;">
                @csrf
                <div class="grid-2">
                    <div class="form-group">
                        <label>Titre</label>
                        <input type="text" name="title" value="{{ old('title') }}" required>
                        @error('title') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>Étape liée (facultatif)</label>
                        <select name="project_step_id">
                            <option value="">— aucune —</option>
                            @foreach ($p->steps as $s) 
                                <option value="{{ $s->id }}">{{ $s->name }}</option> 
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Contenu</label>
                    <textarea name="content" rows="3">{{ old('content') }}</textarea>
                </div>
                <label class="check">
                    <input type="checkbox" name="is_visible_to_client" value="1" checked> 
                    Visible par le client
                </label>
                <button class="btn btn-primary">Publier</button>
            </form>
        @endcan

        @forelse ($p->updates as $u)
            <div class="entry">
                <div class="actions" style="justify-content: space-between;">
                    <strong>{{ $u->title }}</strong>
                    <span class="muted">
                        {{ $u->created_at->format('d/m/Y H:i') }} · {{ $u->author?->name }}
                        @if ($u->step) · {{ $u->step->name }} @endif
                        · {{ $u->is_visible_to_client ? 'Visible client' : 'Interne' }}
                    </span>
                </div>
                @if ($u->content) <p>{{ $u->content }}</p> @endif

                @can('publishUpdate', $p)
                    <details>
                        <summary>Modifier</summary>
                        <form method="POST" action="{{ route('admin.projects.journal.update', [$p, $u]) }}" style="margin-top: .5rem;">
                            @csrf 
                            @method('PUT')
                            <input type="text" name="title" value="{{ $u->title }}" required>
                            <select name="project_step_id" style="margin: .5rem 0;">
                                <option value="">— aucune étape —</option>
                                @foreach ($p->steps as $s) 
                                    <option value="{{ $s->id }}" @selected($u->project_step_id === $s->id)>{{ $s->name }}</option> 
                                @endforeach
                            </select>
                            <textarea name="content" rows="3">{{ $u->content }}</textarea>
                            <label class="check">
                                <input type="checkbox" name="is_visible_to_client" value="1" @checked($u->is_visible_to_client)> 
                                Visible par le client
                            </label>
                            <button class="btn btn-primary">Enregistrer</button>
                        </form>
                        <form method="POST" action="{{ route('admin.projects.journal.destroy', [$p, $u]) }}" onsubmit="return confirm('Supprimer cette actualité ?')" style="margin-top: .5rem;">
                            @csrf 
                            @method('DELETE')
                            <button class="btn btn-ghost">Supprimer</button>
                        </form>
                    </details>
                @endcan
            </div>
        @empty
            <p class="muted">Aucune actualité.</p>
        @endforelse
    </div>

    {{-- ========================================================= --}}
    {{-- Photos --}}
    {{-- ========================================================= --}}
    <div class="card" style="margin-top: 1rem;">
        <h3>Photos</h3>

        @can('managePhotos', $p)
            <form method="POST" action="{{ route('admin.projects.photos.store', $p) }}" enctype="multipart/form-data" style="margin-bottom: 1rem;">
                @csrf
                <div class="grid-2">
                    <div class="form-group">
                        <label>Photos (10 max, JPG/PNG/WEBP, 10 Mo)</label>
                        <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" required>
                        @error('photos') <p class="error">{{ $message }}</p> @enderror
                        @error('photos.*') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>Légende</label>
                        <input type="text" name="caption" value="{{ old('caption') }}">
                    </div>
                </div>
                <div class="inline-form">
                    <select name="project_step_id">
                        <option value="">— aucune étape —</option>
                        @foreach ($p->steps as $s) 
                            <option value="{{ $s->id }}">{{ $s->name }}</option> 
                        @endforeach
                    </select>
                    <label class="check">
                        <input type="checkbox" name="is_visible_to_client" value="1" checked> 
                        Visible par le client
                    </label>
                    <button class="btn btn-primary">Téléverser</button>
                </div>
            </form>
        @endcan

        <div class="photo-grid">
            @forelse ($p->photos as $ph)
                <div class="photo">
                    <a href="{{ route('files.photo', $ph) }}" target="_blank">
                        <img src="{{ route('files.photo', $ph) }}" alt="{{ $ph->caption }}" loading="lazy">
                    </a>
                    <p class="muted">
                        {{ $ph->caption ?: 'Sans légende' }}<br>
                        {{ $ph->is_visible_to_client ? 'Visible client' : 'Interne' }}
                    </p>
                    @can('managePhotos', $p)
                        <details>
                            <summary>Modifier</summary>
                            <form method="POST" action="{{ route('admin.projects.photos.update', [$p, $ph]) }}">
                                @csrf 
                                @method('PUT')
                                <input type="text" name="caption" value="{{ $ph->caption }}" placeholder="Légende">
                                <select name="project_step_id" style="margin: .5rem 0;">
                                    <option value="">— aucune étape —</option>
                                    @foreach ($p->steps as $s) 
                                        <option value="{{ $s->id }}" @selected($ph->project_step_id === $s->id)>{{ $s->name }}</option> 
                                    @endforeach
                                </select>
                                <label class="check">
                                    <input type="checkbox" name="is_visible_to_client" value="1" @checked($ph->is_visible_to_client)> 
                                    Visible client
                                </label>
                                <button class="btn btn-primary">OK</button>
                            </form>
                            <form method="POST" action="{{ route('admin.projects.photos.destroy', [$p, $ph]) }}" onsubmit="return confirm('Supprimer cette photo ?')" style="margin-top: .5rem;">
                                @csrf 
                                @method('DELETE')
                                <button class="btn btn-ghost">Supprimer</button>
                            </form>
                        </details>
                    @endcan
                </div>
            @empty
                <p class="muted">Aucune photo.</p>
            @endforelse
        </div>
    </div>
@endsection