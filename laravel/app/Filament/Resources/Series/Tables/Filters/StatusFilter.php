<?php

namespace App\Filament\Resources\Series\Tables\Filters;

use App\Models\Series;
use App\Models\TheTvDB\SeriesData;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class StatusFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make(Series::has_one_data . '.' . SeriesData::status)
            ->label('Status')
            ->placeholder('Alle Status')
            ->options([
                'Ended' => 'Beendet',
                'Continuing' => 'Laufend',
                'Upcoming' => 'Geplant',
            ])
            ->modifyQueryUsing(function (Builder $query, $state) {
                if (filled($state['value'] ?? null)) {
                    $query->whereHas(Series::has_one_data, function (Builder $q) use ($state) {
                        $q->where(SeriesData::status, $state['value']);
                    });
                }
            });
    }
}

