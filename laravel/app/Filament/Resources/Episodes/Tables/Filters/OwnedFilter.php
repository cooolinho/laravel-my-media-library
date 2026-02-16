<?php

namespace App\Filament\Resources\Episodes\Tables\Filters;

use App\Models\Episode;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

class OwnedFilter
{
    public static function make(): TernaryFilter
    {
        return TernaryFilter::make(Episode::owned)
            ->label('Im Besitz')
            ->placeholder('Alle Episoden')
            ->trueLabel('Nur im Besitz')
            ->falseLabel('Nicht im Besitz')
            ->queries(
                true: fn(Builder $query) => $query->where(Episode::owned, true),
                false: fn(Builder $query) => $query->where(Episode::owned, false),
                blank: fn(Builder $query) => $query,
            );
    }
}

