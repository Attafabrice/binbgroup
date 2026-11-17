<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FieldType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use App\Support\Reorder;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(){
        $this->authorize('viewAny', Service::class);
        return view('admin.services.index', [
            'services' => Service::withCount(['quoteRequests', 'projects'])->orderBy('position')->orderBy('id')->get(),
        ]);
    }

    public function create(){
        $this->authorize('create', Service::class);
        return view('admin.services.form', ['service' => new Service(['is_active' => true]), 'types' => FieldType::cases()]);
    }

    public function store(ServiceRequest $request){
        $this->authorize('create', Service::class);
        $service = Service::create($this->payload($request) + ['position' => (Service::max('position') ?? 0) + 1]);
        return redirect()->route('admin.services.edit', $service)
            ->with('success', 'Service créé. Ajoute maintenant ses champs et ses étapes types.');
    }

    public function edit(Service $service){
        $this->authorize('view', $service);
        $service->load(['fields', 'stepTemplates']);
        return view('admin.services.form', ['service' => $service, 'types' => FieldType::cases()]);
    }

    public function update(ServiceRequest $request, Service $service){
        $this->authorize('update', $service);
        $service->update($this->payload($request, $service));
        return back()->with('success', 'Service mis à jour.');
    }

    public function destroy(Service $service){
        $this->authorize('delete', $service);

        if ($service->quoteRequests()->exists() || $service->projects()->exists() || $service->quotes()->exists()) {
            return back()->with('error', 'Ce service est utilisé par des demandes, devis ou projets : désactive-le au lieu de le supprimer.');
        }

        $this->deleteImage($service);
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service supprimé.');
    }

    public function move(Service $service, string $direction){
        $this->authorize('update', $service);
        Reorder::move($service, $direction, Service::query());

        return back();
    }

    private function payload(ServiceRequest $request, ?Service $service = null): array{
        $data = $request->safe()->only(['name', 'lead', 'description']);
        $data['is_active'] = $request->boolean('is_active');

        if ($file = $request->file('image')) {
            $name = Str::uuid().'.'.$file->extension();
            $file->move(public_path('assets/images/services'), $name); // dossier public : l'image s'affiche sur le site
            if ($service) {
                $this->deleteImage($service);
            }
            $data['image'] = 'services/'.$name;
        }

        return $data;
    }

    private function deleteImage(Service $service): void{
        // on ne supprime que les images uploadées d'ici, jamais celles posées à la main
        if ($service->image && str_starts_with($service->image, 'services/')) {
            @unlink(public_path('assets/images/'.$service->image));
        }
    }
}