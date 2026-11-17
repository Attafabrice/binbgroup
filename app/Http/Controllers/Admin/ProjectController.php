<?php

namespace App\Http\Controllers\Admin;

use App\Enums\{ProjectStatus, StepStatus};
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\{StoreStepRequest, UpdateProjectRequest, UpdateStepRequest};
use App\Models\{Project, ProjectStep, User};
use App\Services\ProjectService;
use DomainException;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $service) {}

    public function index(Request $request){
        $this->authorize('viewAny', Project::class);
        $projects = Project::with(['client', 'service', 'manager'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q
                ->where('reference', 'like', '%'.$request->q.'%')
                ->orWhere('title', 'like', '%'.$request->q.'%')))
            ->latest()->paginate(15)->withQueryString();
        return view('admin.projects.index', ['projects' => $projects, 'statuses' => ProjectStatus::cases()]);
    }

    public function show(Project $project){
        $this->authorize('view', $project);
        $project->load(['client', 'service', 'manager', 'quote.items', 'steps']);

        return view('admin.projects.show', [
            'p'         => $project,
            'statuses'  => ProjectStatus::cases(),
            'stepStatuses' => StepStatus::cases(),
            'managers'  => User::role(['super_admin', 'admin', 'chef_projet'])->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project){
        $this->authorize('update', $project);
        return $this->run(fn () => $this->service->update($project, $request->validated()), 'Projet mis à jour.');
    }

    public function storeStep(StoreStepRequest $request, Project $project){
        $this->authorize('manageSteps', $project);
        return $this->run(fn () => $this->service->addStep($project, $request->name, (int) $request->weight), 'Étape ajoutée.');
    }

    public function updateStep(UpdateStepRequest $request, Project $project, ProjectStep $step){
        $this->authorize('manageSteps', $project);
        abort_unless($step->project_id === $project->id, 404);

        $data = $request->validated() + ['is_visible_to_client' => $request->boolean('is_visible_to_client')];
        return $this->run(fn () => $this->service->updateStep($step, $data), 'Étape mise à jour.');
    }

    public function destroyStep(Project $project, ProjectStep $step){
        $this->authorize('manageSteps', $project);
        abort_unless($step->project_id === $project->id, 404);

        return $this->run(fn () => $this->service->deleteStep($step), 'Étape supprimée.');
    }

    private function run(\Closure $action, string $ok){
        try {
            $action();
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', $ok);
    }
}