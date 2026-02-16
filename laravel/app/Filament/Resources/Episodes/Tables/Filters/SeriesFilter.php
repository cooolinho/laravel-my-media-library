<?php

namespace App\Filament\Resources\Episodes\Tables\Filters;

use App\Models\Episode;
use App\Models\Series;
use Filament\Tables\Filters\SelectFilter;

class SeriesFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make(Episode::series_id)
            ->label('Serie')
            ->placeholder('Alle Serien')
            ->searchable()
            ->preload()
            ->options(
                Series::query()
                    ->orderBy(Series::name, 'ASC')
                    ->get()
                    ->pluck(Series::name, Series::id)
            );
    }
}

