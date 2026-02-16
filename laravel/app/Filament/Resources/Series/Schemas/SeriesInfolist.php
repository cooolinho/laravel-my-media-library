<?php

namespace App\Filament\Resources\Series\Schemas;

use App\Models\Series;
use App\Models\TheTvDB\SeriesData;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SeriesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make(Series::has_one_data . '.' . SeriesData::image)
                    ->label('Preview')
                    ->imageHeight('300px')
                    ->columnSpanFull(),
                TextEntry::make(Series::has_one_data . '.' . SeriesData::translated_name)
                    ->label('Name'),
                TextEntry::make(Series::has_one_data . '.' . SeriesData::year)
                    ->label('Year'),
                TextEntry::make(Series::has_one_data . '.' . SeriesData::firstAired)
                    ->label('First Aired'),
                TextEntry::make(Series::has_one_data . '.' . SeriesData::lastAired)
                    ->label('Last Aired'),
                TextEntry::make(Series::has_one_data . '.' . SeriesData::status)
                    ->label('Status'),
                TextEntry::make(Series::has_one_data . '.' . SeriesData::originalCountry)
                    ->label('Country'),
                TextEntry::make(Series::has_one_data . '.' . SeriesData::originalLanguage)
                    ->label('Language'),
                TextEntry::make(Series::has_one_data . '.' . SeriesData::averageRuntime)
                    ->label('Average Runtime'),
                TextEntry::make(Series::has_one_data . '.' . SeriesData::overview)
                    ->label('Overview')
                    ->columnSpanFull(),
            ]);
    }
}
