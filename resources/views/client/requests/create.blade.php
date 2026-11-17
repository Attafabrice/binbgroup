@extends('layouts.client')

@section('title', 'Nouvelle demande de devis')
@section('nav') @include('client._nav') @endsection

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    
    @if (! $service)
        {{-- ========================================== --}}
        {{-- ÉTAPE 1 : Choix du service                 --}}
        {{-- ========================================== --}}
        <form method="GET" action="{{ route('client.requests.create') }}">
            <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Quel service vous intéresse ?</h2>
            
            <div class="form-group">
                <label for="service">Service souhaité</label>
                <select name="service" id="service" required style="width:100%">
                    <option value="">— Choisir un service —</option>
                    @foreach ($services as $s) 
                        <option value="{{ $s->id }}">{{ $s->name }}</option> 
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">
                Continuer <i class="fas fa-arrow-right" style="margin-left: 0.5rem;"></i>
            </button>
        </form>
        
    @else
        {{-- ========================================== --}}
        {{-- ÉTAPE 2 : Formulaire dynamique             --}}
        {{-- ========================================== --}}
        <form method="POST" action="{{ route('client.requests.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="service_id" value="{{ $service->id }}">

            <!-- En-tête du service sélectionné -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--c-border);">
                <div>
                    <span class="muted" style="font-size: 0.85rem;">Service sélectionné</span>
                    <h2 style="margin: 0; font-size: 1.25rem; color: var(--c-primary);">{{ $service->name }}</h2>
                </div>
                <a href="{{ route('client.requests.create') }}" class="btn btn-ghost" style="font-size: 0.85rem;">
                    <i class="fas fa-exchange-alt"></i> Changer
                </a>
            </div>
            @error('service_id') <p class="error">{{ $message }}</p> @enderror

            <!-- Champs dynamiques -->
            @foreach ($service->fields as $f)
                @php $key = 'answers.'.$f->name; $n = 'answers['.$f->name.']'; @endphp
                <div class="form-group">
                    <label>
                        {{ $f->label }} 
                        @if($f->is_required)
                            <span class="muted" style="font-weight: normal;">(obligatoire)</span>
                        @endif
                    </label>
                    
                    @switch($f->type->value)
                        @case('textarea')
                            <textarea name="{{ $n }}" rows="3" @required($f->is_required) placeholder="Votre réponse...">{{ old($key) }}</textarea> 
                            @break
                        @case('number')
                            <input type="number" step="any" name="{{ $n }}" value="{{ old($key) }}" @required($f->is_required)> 
                            @break
                        @case('date')
                            <input type="date" name="{{ $n }}" value="{{ old($key) }}" @required($f->is_required)> 
                            @break
                        @case('select')
                            <select name="{{ $n }}" @required($f->is_required) style="width:100%">
                                <option value="">— Choisir —</option>
                                @foreach ($f->options ?? [] as $o) 
                                    <option value="{{ $o }}" @selected(old($key) === $o)>{{ $o }}</option> 
                                @endforeach
                            </select> 
                            @break
                        @case('file')
                            <div style="background: var(--c-bg); padding: 1rem; border-radius: var(--radius); border: 1px dashed var(--c-border);">
                                <input type="file" name="{{ $n }}" accept=".pdf,.jpg,.jpeg,.png,.webp" @required($f->is_required) style="font-size: 0.9rem;">
                                <p class="muted" style="margin: 0.5rem 0 0; font-size: 0.8rem;">PDF, JPG, PNG ou WEBP, 10 Mo maximum.</p>
                            </div>
                            @break
                        @default
                            <input type="text" name="{{ $n }}" value="{{ old($key) }}" @required($f->is_required)>
                    @endswitch
                    
                    @error($key) <p class="error">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <!-- Message -->
            <div class="form-group" style="margin-top: 1.5rem;">
                <label>Précisions complémentaires</label>
                <textarea name="message" rows="4" placeholder="Décrivez votre projet en quelques mots...">{{ old('message') }}</textarea>
                @error('message') <p class="error">{{ $message }}</p> @enderror
            </div>

            <!-- Pièces jointes -->
            <div class="form-group" style="margin-top: 1.5rem;">
                <label>Autres documents <span class="muted" style="font-weight: normal;">(facultatif)</span></label>
                <div style="background: var(--c-bg); padding: 1rem; border-radius: var(--radius); border: 1px dashed var(--c-border);">
                    <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp" style="font-size: 0.9rem;">
                    <p class="muted" style="margin: 0.5rem 0 0; font-size: 0.8rem;">Plans, photos, titre foncier… 5 fichiers maximum, 10 Mo chacun.</p>
                </div>
                @error('attachments') <p class="error">{{ $message }}</p> @enderror
                @foreach ($errors->get('attachments.*') as $msgs) 
                    @foreach ($msgs as $m) <p class="error">{{ $m }}</p> @endforeach 
                @endforeach
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 1rem; margin-top: 2rem; justify-content: flex-end;">
                <a class="btn btn-ghost" href="{{ route('client.requests.index') }}">Annuler</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">
                    <i class="fas fa-paper-plane" style="margin-right: 0.5rem;"></i> Envoyer ma demande
                </button>
            </div>
        </form>
    @endif
    
</div>
@endsection