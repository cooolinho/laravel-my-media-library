<?php

namespace App\Filament\Resources\Episodes\Tables\Filters;

use App\Models\Episode;
use App\Models\TheTvDB\EpisodeData;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class WithoutNameFilter
{
    public static function make(): Filter
    {
        return Filter::make('without_name')
            ->label('Ohne Titel')
            ->toggle()
            ->query(function (Builder $query) {
                $query->whereHas(Episode::has_one_data, function (Builder $q) {
                    $q->where(function (Builder $subQ) {
                        $subQ->whereNull(EpisodeData::translations)
                            ->orWhereRaw("JSON_LENGTH(JSON_EXTRACT(translations, '$.deu.name')) = 0")
                            ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(translations, '$.deu.name')) = ''");
                    });
                })->orDoesntHave(Episode::has_one_data);
            });
    }
}

