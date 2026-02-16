<?php

namespace App\Filament\Resources\Episodes\Tables\Filters;

use App\Models\Episode;
use Filament\Tables\Filters\SelectFilter;

class SeasonFilter
{
    public static function make(int $seriesId = null): SelectFilter
    {
        return SelectFilter::make(Episode::seasonNumber)
            ->label('Staffel')
            ->placeholder('Alle Staffeln')
            ->options(function () use ($seriesId) {
                return Episode::query()
                    ->when($seriesId, fn($query) => $query->where(Episode::series_id, $seriesId))
                    ->distinct()
                    ->orderBy(Episode::seasonNumber)
                    ->pluck(Episode::seasonNumber, Episode::seasonNumber)
                    ->mapWithKeys(fn($season) => [$season => "Staffel {$season}"])
                    ->toArray();
            });
    }
}

