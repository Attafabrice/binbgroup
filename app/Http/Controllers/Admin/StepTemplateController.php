<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StepTemplateRequest;
use App\Models\{Service, StepTemplate};
use App\Support\Reorder;

class StepTemplateController extends Controller
{
    public function store(StepTemplateRequest $request, Service $service){
        $this->authorize('update', $service);
        $service->stepTemplates()->create($request->validated());
        return back()->with('success', 'Étape ajoutée.');
    }

    public function update(StepTemplateRequest $request, Service $service, StepTemplate $step){
        $this->authorize('update', $service);
        abort_unless($step->service_id === $service->id, 404);
        $step->update($request->validated());
        return back()->with('success', 'Étape mise à jour.');
    }

    public function destroy(Service $service, StepTemplate $step){
        $this->authorize('update', $service);
        abort_unless($step->service_id === $service->id, 404);
        $step->delete(); // les projets déjà créés gardent leurs étapes (copiées)
        return back()->with('success', 'Étape supprimée.');
    }

    public function move(Service $service, StepTemplate $step, string $direction){
        $this->authorize('update', $service);
        abort_unless($step->service_id === $service->id, 404);
        Reorder::move($step, $direction, $service->stepTemplates()->getQuery()->reorder());

        return back();
    }
}