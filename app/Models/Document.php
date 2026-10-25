<?php

namespace App\Models;

use App\Enums\DocumentOrigin;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Document extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'origin'               => DocumentOrigin::class,
        'type'                 => DocumentType::class,
        'is_visible_to_client' => 'boolean',
        'visible_at'           => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Document $d) {
            $d->client_id ??= match (true) {
                $d->documentable instanceof Client => $d->documentable->id,
                default => $d->documentable?->client_id,
            };
        });

        static::saving(function (Document $d) {
            if ($d->is_visible_to_client && ! $d->visible_at) {
                $d->visible_at = now();
            }
        });
    }

    public function scopeVisibleToClient(Builder $q): Builder
    {
        return $q->where('is_visible_to_client', true);
    }

    public function download(): StreamedResponse
    {
        return Storage::disk($this->disk)->download($this->file_path, $this->original_name);
    }

    public function documentable(): MorphTo { return $this->morphTo(); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}