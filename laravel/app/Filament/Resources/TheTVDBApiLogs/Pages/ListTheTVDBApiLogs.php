<?php

namespace App\Filament\Resources\TheTVDBApiLogs\Pages;

use App\Filament\Resources\TheTVDBApiLogs\TheTVDBApiLogResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListTheTVDBApiLogs extends ListRecords
{
    protected static string $resource = TheTVDBApiLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // action to statistics page
            Action::make('statistics')
                ->label('Statistics')
                ->icon(Heroicon::OutlinedChartBar)
                ->url(TheTVDBApiLogResource::getUrl('statistics')),
        ];
    }
}

