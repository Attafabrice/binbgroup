<?php

namespace App\Models;

use App\Enums\FieldType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ServiceField extends Model
{
    protected $guarded = [];

    protected $casts = [
        'type'        => FieldType::class,
        'options'     => 'array',
        'is_required' => 'boolean',
    ];

    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
}