<?php

namespace App\Filament\Resources\EpisodeResource\Pages;

use App\Filament\Resources\EpisodeResource;
use App\Jobs\EpisodeDataJob;
use App\Models\Episode;
use App\Models\TheTvDB\EpisodeData;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewEpisode extends ViewRecord
{
    protected static string $resource = EpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('loadEpisodeData')
                ->requiresConfirmation()
                ->action(fn (Episode $episode) => EpisodeDataJob::dispatch($episode))
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\ImageEntry::make(Episode::has_one_data . '.' . EpisodeData::image)
                    ->label('Preview')
                    ->height('300px')
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make(Episode::has_one_data . '.' . EpisodeData::name)
                    ->label('Name'),
                Infolists\Components\IconEntry::make(Episode::owned)
                    ->boolean()
                    ->falseColor('danger')
                    ->trueColor('success')
                    ->label('Owned'),
                Infolists\Components\TextEntry::make(Episode::seasonNumber)
                    ->label('Season'),
                Infolists\Components\TextEntry::make(Episode::number)
                    ->label('Number'),
                Infolists\Components\TextEntry::make(Episode::has_one_data . '.' . EpisodeData::overview)
                    ->label('Overview')
                    ->columnSpanFull(),
            ]);
    }
}
