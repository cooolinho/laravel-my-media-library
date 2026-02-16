<?php

namespace App\Filament\Resources\Series\Tables\Filters;

use App\Models\Series;
use App\Models\TheTvDB\SeriesData;
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
                return SeriesData::query()
                    ->whereNotNull(SeriesData::year)
                    ->distinct()
                    ->orderBy(SeriesData::year, 'DESC')
                    ->pluck(SeriesData::year, SeriesData::year)
                    ->toArray();
            })
            ->modifyQueryUsing(function (Builder $query, $state) {
                if (filled($state['value'] ?? null)) {
                    $query->whereHas(Series::has_one_data, function (Builder $q) use ($state) {
                        $q->where(SeriesData::year, $state['value']);
                    });
                }
            });
    }
}

