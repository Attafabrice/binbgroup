<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\{StoreProjectPhotosRequest, UpdateProjectPhotoRequest};
use App\Models\{Project, ProjectPhoto};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectPhotoController extends Controller
{
    public function store(StoreProjectPhotosRequest $request, Project $project){
        $this->authorize('managePhotos', $project);
        foreach ($request->file('photos') as $file) {
            // nom généré : jamais le nom d'origine
            $path = $file->storeAs("project-photos/{$project->id}", Str::uuid().'.'.$file->extension(), 'local');
            $project->photos()->create([
                'disk'                 => 'local',
                'path'                 => $path,
                'caption'              => $request->input('caption'),
                'project_step_id'      => $request->input('project_step_id'),
                'taken_at'             => now(),
                'is_visible_to_client' => $request->boolean('is_visible_to_client'),
                'uploaded_by'          => auth()->id(),
            ]);
        }
        return back()->with('success', count($request->file('photos')).' photo(s) ajoutée(s).');
    }

    public function update(UpdateProjectPhotoRequest $request, Project $project, ProjectPhoto $photo){
        $this->authorize('managePhotos', $project);
        abort_unless($photo->project_id === $project->id, 404);

        $photo->update([
            ...$request->validated(),
            'is_visible_to_client' => $request->boolean('is_visible_to_client'),
        ]);
        return back()->with('success', 'Photo mise à jour.');
    }

    public function destroy(Project $project, ProjectPhoto $photo){
        $this->authorize('managePhotos', $project);
        abort_unless($photo->project_id === $project->id, 404);

        $disk = $photo->disk;
        $files = array_filter([$photo->path, $photo->thumbnail_path]);
        $photo->delete();
        Storage::disk($disk)->delete($files);

        return back()->with('success', 'Photo supprimée.');
    }
}