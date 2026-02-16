<?php

namespace App\Filament\Resources\Episodes\Tables\Filters;

use App\Models\Episode;
use Filament\Tables\Filters\SelectFilter;

class SeasonFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make(Episode::seasonNumber)
            ->label('Staffel')
            ->placeholder('Alle Staffeln')
            ->options(function () {
                return Episode::query()
                    ->distinct()
                    ->orderBy(Episode::seasonNumber)
                    ->pluck(Episode::seasonNumber, Episode::seasonNumber)
                    ->mapWithKeys(fn($season) => [$season => "Staffel {$season}"])
                    ->toArray();
            });
    }
}

