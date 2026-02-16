<?php

namespace App\Filament\Resources\Series\Tables\Filters;

use App\Models\Series;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class WithoutDataFilter
{
    public static function make(): Filter
    {
        return Filter::make('without_data')
            ->label('Ohne Metadaten')
            ->toggle()
            ->query(fn(Builder $query) => $query->doesntHave(Series::has_one_data));
    }
}

