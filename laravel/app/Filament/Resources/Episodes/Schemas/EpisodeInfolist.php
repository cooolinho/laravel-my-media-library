<?php

namespace App\Filament\Resources\Episodes\Schemas;

use App\Models\Episode;
use App\Models\TheTvDB\EpisodeData;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EpisodeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make(Episode::has_one_data . '.' . EpisodeData::image)
                    ->label('Preview')
                    ->imageHeight('300px')
                    ->columnSpanFull(),
                TextEntry::make(Episode::has_one_data . '.' . EpisodeData::translated_name)
                    ->label('Name'),
                IconEntry::make(Episode::owned)
                    ->boolean()
                    ->falseColor('danger')
                    ->trueColor('success')
                    ->label('Owned'),
                TextEntry::make(Episode::seasonNumber)
                    ->label('Season'),
                TextEntry::make(Episode::number)
                    ->label('Number'),
                TextEntry::make(Episode::has_one_data . '.' . EpisodeData::overview)
                    ->label('Overview')
                    ->columnSpanFull(),
            ]);
    }
}
