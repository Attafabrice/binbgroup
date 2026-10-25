<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Enums\StepStatus;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Project extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'status'           => ProjectStatus::class,
        'start_date'       => 'date',
        'planned_end_date' => 'date',
        'actual_end_date'  => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Project $p) {
            $p->reference ??= Sequence::next('project');
            $p->status ??= ProjectStatus::Planifie;
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public static function createFromQuote(Quote $quote, ?int $serviceId = null): self
    {
        $serviceId ??= $quote->quoteRequest?->service_id;
        if (! $serviceId) {
            throw new DomainException('Service introuvable pour créer le projet.');
        }

        return DB::transaction(function () use ($quote, $serviceId) {
            $project = static::create([
                'client_id'  => $quote->client_id,
                'service_id' => $serviceId,
                'quote_id'   => $quote->id,
                'title'      => $quote->title,
            ]);

            StepTemplate::where('service_id', $serviceId)->orderBy('position')->get()
                ->each(fn (StepTemplate $t) => $project->steps()->create([
                    'name' => $t->name, 'weight' => $t->weight, 'position' => $t->position,
                ]));

            return $project;
        });
    }

    public function recalculateProgress(): void
    {
        $steps = $this->steps()->get();
        $total = $steps->sum('weight');
        $done  = $steps->where('status', StepStatus::Termine)->sum('weight');

        $this->forceFill(['progress' => $total ? (int) round($done / $total * 100) : 0])->saveQuietly();
    }

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
    public function quote(): BelongsTo { return $this->belongsTo(Quote::class); }
    public function manager(): BelongsTo { return $this->belongsTo(User::class, 'manager_id'); }
    public function team(): BelongsToMany { return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps(); }
    public function steps(): HasMany { return $this->hasMany(ProjectStep::class)->orderBy('position'); }
    public function updates(): HasMany { return $this->hasMany(ProjectUpdate::class)->latest(); }
    public function photos(): HasMany { return $this->hasMany(ProjectPhoto::class)->latest(); }
    public function interventions(): HasMany { return $this->hasMany(Intervention::class); }
    public function documents(): MorphMany { return $this->morphMany(Document::class, 'documentable'); }
}