@extends('layouts.admin')
@section('title', $c->display_name)
@section('nav') @include('admin._nav') @endsection

@section('content')
@php $company = $c->type === \App\Enums\ClientType::Entreprise; @endphp

<div class="card" style="margin-bottom:1rem">
    <div class="actions" style="justify-content:space-between">
        <div>
            <strong>{{ $c->display_name }}</strong> · {{ ucfirst($c->type->value) }} · client depuis le {{ $c->created_at->format('d/m/Y') }}
        </div>
        <a class="btn btn-ghost" href="{{ route('admin.clients.index') }}">← Retour</a>
    </div>
</div>

<div class="card" style="margin-bottom:1rem">
    <h3>Compte de connexion</h3>
    @if ($c->user)
        <p>
            @if ($c->user->is_active) <span class="badge badge-success">Actif</span> @else <span class="badge badge-danger">Suspendu</span> @endif
            · {{ $c->user->email }}
            · <span class="muted">dernière connexion : {{ $c->user->last_login_at?->format('d/m/Y H:i') ?? 'jamais' }}</span>
        </p>
        @can('manageAccount', $c)
        <div class="actions" style="margin-top:.75rem">
            <form method="POST" action="{{ route('admin.clients.toggle', $c) }}"
                  onsubmit="return confirm('{{ $c->user->is_active ? 'Suspendre ce compte ?' : 'Réactiver ce compte ?' }}')">
                @csrf<button class="btn {{ $c->user->is_active ? 'btn-danger' : 'btn-success' }}">{{ $c->user->is_active ? 'Suspendre' : 'Réactiver' }}</button>
            </form>
            <form method="POST" action="{{ route('admin.clients.reset', $c) }}" onsubmit="return confirm('Envoyer un lien de réinitialisation à ce client ?')">
                @csrf<button class="btn btn-ghost">Envoyer un lien de réinitialisation</button>
            </form>
        </div>
        @endcan
    @else
        <p class="muted">Ce client n'a pas de compte de connexion.</p>
    @endif
</div>

<form method="POST" action="{{ route('admin.clients.update', $c) }}" class="card" style="margin-bottom:1rem">
    @csrf @method('PUT')
    <h3>Informations</h3>
    @cannot('update', $c) <fieldset disabled style="border:0"> @endcannot
    <div class="grid-2">
        @if ($company)
        <div class="form-group"><label>Entreprise</label>
            <input type="text" name="company_name" value="{{ old('company_name', $c->company_name) }}" required>
            @error('company_name') <p class="error">{{ $message }}</p> @enderror</div>
        @endif
        <div class="form-group"><label>Contact</label>
            <input type="text" name="contact_name" value="{{ old('contact_name', $c->contact_name) }}" required>
            @error('contact_name') <p class="error">{{ $message }}</p> @enderror</div>
        <div class="form-group"><label>Téléphone</label>
            <input type="text" name="phone" value="{{ old('phone', $c->phone) }}" required>
            @error('phone') <p class="error">{{ $message }}</p> @enderror</div>
        <div class="form-group"><label>Email</label>
            <input type="email" name="email" value="{{ old('email', $c->email) }}">
            @error('email') <p class="error">{{ $message }}</p> @enderror</div>
        <div class="form-group"><label>Adresse</label>
            <input type="text" name="address" value="{{ old('address', $c->address) }}"></div>
        <div class="form-group"><label>Ville</label>
            <input type="text" name="city" value="{{ old('city', $c->city) }}"></div>
        @if ($company)
        <div class="form-group"><label>RCCM</label><input type="text" name="rccm" value="{{ old('rccm', $c->rccm) }}"></div>
        <div class="form-group"><label>NCC</label><input type="text" name="ncc" value="{{ old('ncc', $c->ncc) }}"></div>
        @endif
    </div>
    <div class="form-group"><label>Notes internes (jamais visibles par le client)</label>
        <textarea name="notes" rows="3">{{ old('notes', $c->notes) }}</textarea></div>
    @can('update', $c) <button class="btn btn-primary">Enregistrer</button> @endcan
    @cannot('update', $c) </fieldset> @endcannot
</form>

<div class="grid-2">
    <div class="card">
        <h3>Demandes ({{ $c->quoteRequests->count() }})</h3>
        @forelse ($c->quoteRequests as $r)
            <p><a href="{{ route('admin.quote-requests.show', $r) }}">{{ $r->reference }}</a> · {{ $r->service?->name }} <x-status-badge :status="$r->status" /></p>
        @empty <p class="muted">Aucune.</p> @endforelse
    </div>
    <div class="card">
        <h3>Devis ({{ $c->quotes->count() }})</h3>
        @forelse ($c->quotes as $q)
            <p><a href="{{ route('admin.quotes.show', $q) }}">{{ $q->reference }}</a> · {{ \App\Models\Quote::money($q->total) }} <x-status-badge :status="$q->status" /></p>
        @empty <p class="muted">Aucun.</p> @endforelse
    </div>
</div>

<div class="card" style="margin-top:1rem">
    <h3>Projets ({{ $c->projects->count() }})</h3>
    @forelse ($c->projects as $p)
        <p><a href="{{ route('admin.projects.show', $p) }}">{{ $p->reference }}</a> · {{ $p->title }} · {{ $p->progress }} % <x-status-badge :status="$p->status" /></p>
    @empty <p class="muted">Aucun.</p> @endforelse
</div>

@can('delete', $c)
<div class="card" style="margin-top:1rem">
    <h3>Zone sensible</h3>
    <form method="POST" action="{{ route('admin.clients.destroy', $c) }}" onsubmit="return confirm('Supprimer définitivement ce client ?')">
        @csrf @method('DELETE')<button class="btn btn-danger">Supprimer le client</button>
    </form>
    <p class="muted">Refusé si le client a des demandes, devis ou projets.</p>
</div>
@endcan
@endsection