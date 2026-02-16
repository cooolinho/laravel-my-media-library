<?php

namespace App\Filament\Resources\Series\Tables\Filters;

use App\Models\Series;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class WithoutEpisodesFilter
{
    public static function make(): Filter
    {
        return Filter::make('without_episodes')
            ->label('Ohne Episoden')
            ->toggle()
            ->query(fn(Builder $query) => $query->doesntHave(Series::has_many_episodes));
    }
}

