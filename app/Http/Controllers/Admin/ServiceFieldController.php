<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceFieldRequest;
use App\Models\{Service, ServiceField};
use App\Support\Reorder;

class ServiceFieldController extends Controller
{
    public function store(ServiceFieldRequest $request, Service $service){
        $this->authorize('update', $service);
        $service->fields()->create([ // le nom technique est généré par le modèle
            'label'       => $request->label,
            'type'        => $request->type,
            'options'     => $this->options($request),
            'is_required' => $request->boolean('is_required'),
        ]);
        return back()->with('success', 'Champ ajouté.');
    }

    public function update(ServiceFieldRequest $request, Service $service, ServiceField $field){
        $this->authorize('update', $service);
        abort_unless($field->service_id === $service->id, 404);

        // `name` n'est jamais modifié : il sert de clé aux réponses déjà enregistrées
        $field->update([
            'label'       => $request->label,
            'type'        => $request->type,
            'options'     => $this->options($request),
            'is_required' => $request->boolean('is_required'),
        ]);
        return back()->with('success', 'Champ mis à jour.');
    }

    public function destroy(Service $service, ServiceField $field){
        $this->authorize('update', $service);
        abort_unless($field->service_id === $service->id, 404);
        $field->delete();
        return back()->with('success', 'Champ supprimé.');
    }

    public function move(Service $service, ServiceField $field, string $direction){
        $this->authorize('update', $service);
        abort_unless($field->service_id === $service->id, 404);
        Reorder::move($field, $direction, $service->fields()->getQuery()->reorder());

        return back();
    }

    private function options(ServiceFieldRequest $request): ?array{
        if ($request->type !== 'select') {
            return null;
        }
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $request->options))));
    }
}