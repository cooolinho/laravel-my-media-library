<?php

namespace App\Filament\Resources\Episodes\Tables\Filters;

use App\Models\Episode;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class SpecialsFilter
{
    public static function make(): Filter
    {
        return Filter::make('specials')
            ->label('Nur Specials')
            ->toggle()
            ->query(fn(Builder $query) => $query->where(Episode::seasonNumber, 0));
    }
}

