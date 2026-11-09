<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class StepTemplate extends Model
{
    protected $guarded = [];

    public function service(): BelongsTo { return $this->belongsTo(Service::class); }

    protected static function booted(): void{
        static::creating(function (StepTemplate $t) {
            $t->position ??= (static::where('service_id', $t->service_id)->max('position') ?? 0) + 1;
        });
    }
}