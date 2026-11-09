<?php

namespace App\Models;

use App\Enums\FieldType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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

    public function service(): BelongsTo { 
        return $this->belongsTo(Service::class); 
    }

    protected static function booted(): void{
        static::creating(function (ServiceField $f) {
            if (blank($f->name)) {
                $base = Str::slug($f->label, '_') ?: 'champ';
                $name = $base;
                $i = 2;
                while (static::where('service_id', $f->service_id)->where('name', $name)->exists()) {
                    $name = $base.'_'.$i++;
                }
                $f->name = $name;
            }
            $f->position ??= (static::where('service_id', $f->service_id)->max('position') ?? 0) + 1;
        });
    }
}