<?php

namespace App\Support;

use Illuminate\Database\Eloquent\{Builder, Model};

class Reorder
{
    /** Échange $model avec son voisin, puis renumérote 1..n (sans événements du modèle). */
    public static function move(Model $model, string $direction, Builder $siblings): void
    {
        $list = $siblings->orderBy('position')->orderBy('id')->get()->all();
        $i = collect($list)->search(fn ($m) => $m->id === $model->id);
        $j = $direction === 'up' ? $i - 1 : $i + 1;

        if ($i === false || ! isset($list[$j])) {
            return;
        }
        [$list[$i], $list[$j]] = [$list[$j], $list[$i]];

        foreach ($list as $k => $m) {
            $m->newQuery()->whereKey($m->id)->update(['position' => $k + 1]);
        }
    }
}