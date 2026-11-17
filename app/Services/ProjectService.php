<?php

namespace App\Services;

use App\Enums\{ProjectStatus, StepStatus};
use App\Models\{Project, ProjectStep};
use DomainException;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function update(Project $p, array $data): void{
        $new = ProjectStatus::from($data['status']);

        if ($new === ProjectStatus::Termine && $p->steps()->where('status', '!=', StepStatus::Termine)->exists()) {
            throw new DomainException('Toutes les étapes doivent être terminées avant de clôturer le projet.');
        }
        if ($new === ProjectStatus::EnCours && blank($data['start_date'] ?? $p->start_date)) {
            $data['start_date'] = today();
        }
        if ($new === ProjectStatus::Termine && blank($data['actual_end_date'] ?? $p->actual_end_date)) {
            $data['actual_end_date'] = today();
        }

        $p->update($data);
    }

    public function addStep(Project $p, string $name, int $weight): ProjectStep{
        return DB::transaction(fn () => $p->steps()->create([
            'name'                 => $name,
            'weight'               => $weight,
            'position'             => ($p->steps()->max('position') ?? 0) + 1,
            'is_visible_to_client' => true,
        ]));
    }

    public function updateStep(ProjectStep $step, array $data): void{
        if ($step->status === StepStatus::Termine) {
            throw new DomainException('Une étape terminée ne peut plus être modifiée.');
        }
        $step->update($data);
    }
    
    public function deleteStep(ProjectStep $step): void{
        if ($step->status === StepStatus::Termine) {
            throw new DomainException('Une étape terminée ne peut pas être supprimée.');
        }
        $step->delete();
    }
}