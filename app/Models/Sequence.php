<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Sequence extends Model
{
    protected $guarded = [];

    private const PREFIXES = [
        'quote_request' => 'DEM',
        'quote'         => 'DEV',
        'project'       => 'PRJ',
        'intervention'  => 'INT',
    ];

    public static function next(string $type): string
    {
        $year = (int) now()->format('Y');

        return DB::transaction(function () use ($type, $year) {
            $seq = static::where(['type' => $type, 'year' => $year])->lockForUpdate()->first()
                ?? static::create(['type' => $type, 'year' => $year, 'last' => 0]);

            $seq->increment('last');

            return sprintf('%s-%d-%04d', self::PREFIXES[$type], $year, $seq->last);
        });
    }
}