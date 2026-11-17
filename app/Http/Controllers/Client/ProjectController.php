<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjectController extends Controller
{
    private function clientId(): int{
        return (auth()->user()->client ?? abort(403, 'Aucune fiche client rattachée à ce compte.'))->id;
    }

    public function index(){
        $projects = Project::with('service')
            ->where('client_id', $this->clientId())
            ->latest()->paginate(10);
        return view('client.projects.index', compact('projects'));
    }

    public function show(Project $project){
        abort_unless($project->client_id === $this->clientId(), 404);

        // uniquement ce que l'équipe a marqué « visible client »
        $project->load([
            'service',
            'visibleSteps',
            'visiblePhotos',
            'visibleUpdates.step',
            'quote.items',
            'quote.quoteRequest.visibleDocuments',
        ]);
        return view('client.projects.show', ['p' => $project]);
    }
}