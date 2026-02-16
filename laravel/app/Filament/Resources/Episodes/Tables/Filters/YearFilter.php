<?php

namespace App\Filament\Resources\Episodes\Tables\Filters;

use App\Models\Episode;
use App\Models\TheTvDB\EpisodeData;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class YearFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('year')
            ->label('Jahr')
            ->placeholder('Alle Jahre')
            ->options(function () {
                return EpisodeData::query()
                    ->whereNotNull(EpisodeData::year)
                    ->distinct()
                    ->orderBy(EpisodeData::year, 'DESC')
                    ->pluck(EpisodeData::year, EpisodeData::year)
                    ->toArray();
            })
            ->modifyQueryUsing(function (Builder $query, $state) {
                if (filled($state['value'] ?? null)) {
                    $query->whereHas(Episode::has_one_data, function (Builder $q) use ($state) {
                        $q->where(EpisodeData::year, $state['value']);
                    });
                }
            });
    }
}

