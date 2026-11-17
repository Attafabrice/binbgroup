<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectUpdateRequest;
use App\Models\{Project, ProjectUpdate};

class ProjectJournalController extends Controller
{
    public function store(StoreProjectUpdateRequest $request, Project $project){
        $this->authorize('publishUpdate', $project);
        $project->updates()->create([
            ...$request->validated(),
            'is_visible_to_client' => $request->boolean('is_visible_to_client'),
            'user_id' => auth()->id(),
        ]);
        return back()->with('success', 'Actualité ajoutée.');
    }

    public function update(StoreProjectUpdateRequest $request, Project $project, ProjectUpdate $entry){
        $this->authorize('publishUpdate', $project);
        abort_unless($entry->project_id === $project->id, 404);

        $entry->update([
            ...$request->validated(),
            'is_visible_to_client' => $request->boolean('is_visible_to_client'),
        ]);
        return back()->with('success', 'Actualité modifiée.');
    }

    public function destroy(Project $project, ProjectUpdate $entry){
        $this->authorize('publishUpdate', $project);
        abort_unless($entry->project_id === $project->id, 404);

        $entry->delete();
        return back()->with('success', 'Actualité supprimée.');
    }
}