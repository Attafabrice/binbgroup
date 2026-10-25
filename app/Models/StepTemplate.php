<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class StepTemplate extends Model
{
    protected $guarded = [];

    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
}