<?php

namespace App\Filament\Resources\TheTVDBApiLogs\Pages;

use App\Filament\Resources\TheTVDBApiLogs\TheTVDBApiLogResource;
use App\Services\TheTVDBApiLogger;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;

class TheTVDBApiStatistics extends Page
{
    protected static string $resource = TheTVDBApiLogResource::class;
    protected static ?string $title = 'API Statistics';
    public int $days = 7;
    public array $statistics = [];
    protected string $view = 'filament.resources.the-tvdb-api-logs.pages.statistics';

    public function mount(): void
    {
        $this->loadStatistics();
    }

    public function loadStatistics(): void
    {
        $this->statistics = TheTVDBApiLogger::getStatistics($this->days);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh')
                ->icon(Heroicon::OutlinedArrowPath)
                ->action('loadStatistics'),

            Action::make('7_days')
                ->label('7 Days')
                ->icon(Heroicon::OutlinedCalendar)
                ->iconPosition(IconPosition::Before)
                ->color($this->days === 7 ? 'primary' : 'gray')
                ->action(fn() => $this->updateDays(7)),

            Action::make('30_days')
                ->label('30 Days')
                ->icon(Heroicon::OutlinedCalendar)
                ->iconPosition(IconPosition::Before)
                ->color($this->days === 30 ? 'primary' : 'gray')
                ->action(fn() => $this->updateDays(30)),

            Action::make('90_days')
                ->label('90 Days')
                ->icon(Heroicon::OutlinedCalendar)
                ->iconPosition(IconPosition::Before)
                ->color($this->days === 90 ? 'primary' : 'gray')
                ->action(fn() => $this->updateDays(90)),
        ];
    }

    public function updateDays(int $days): void
    {
        $this->days = $days;
        $this->loadStatistics();
    }
}

