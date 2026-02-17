<?php

namespace App\Filament\Resources\TheTVDBApiLogs\Pages;

use App\Filament\Resources\TheTVDBApiLogs\TheTVDBApiLogResource;
use App\Models\TheTVDBApiLog;
use App\Services\TheTVDBApiLogger;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;

class TheTVDBApiStatistics extends Page
{
    protected static string $resource = TheTVDBApiLogResource::class;
    protected static ?string $title = 'API Statistics';
    public int $days = 7;
    public array $statistics = [];
    public int $endpointsPerPage = 25;
    public int $currentPage = 1;
    public string $sortBy = 'total';
    public string $sortDirection = 'desc';
    protected string $view = 'filament.resources.the-tvdb-api-logs.pages.statistics';

    public function mount(): void
    {
        $this->loadStatistics();
    }

    public function loadStatistics(): void
    {
        $this->statistics = TheTVDBApiLogger::getStatistics($this->days);
        $this->currentPage = 1; // Reset pagination when reloading
    }

    public function getPaginatedEndpoints(): array
    {
        if (empty($this->statistics['requests_by_endpoint'])) {
            return [];
        }

        $endpoints = $this->statistics['requests_by_endpoint'];

        // Sort endpoints
        uasort($endpoints, function ($a, $b) {
            $aVal = $a[$this->sortBy] ?? 0;
            $bVal = $b[$this->sortBy] ?? 0;

            if ($this->sortDirection === 'desc') {
                return $bVal <=> $aVal;
            }
            return $aVal <=> $bVal;
        });

        // Paginate
        $offset = ($this->currentPage - 1) * $this->endpointsPerPage;
        return array_slice($endpoints, $offset, $this->endpointsPerPage, true);
    }

    public function getTotalPages(): int
    {
        if (empty($this->statistics['requests_by_endpoint'])) {
            return 1;
        }
        return (int)ceil(count($this->statistics['requests_by_endpoint']) / $this->endpointsPerPage);
    }

    public function nextPage(): void
    {
        if ($this->currentPage < $this->getTotalPages()) {
            $this->currentPage++;
        }
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function goToPage(int $page): void
    {
        $totalPages = $this->getTotalPages();
        $this->currentPage = max(1, min($page, $totalPages));
    }

    public function sortEndpoints(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }
        $this->currentPage = 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clear_all_logs')
                ->label('Clear All Logs')
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Löschen bestätigen')
                ->modalDescription('Bist du sicher, dass du alle API-Logs löschen möchtest? Diese Aktion kann nicht rückgängig gemacht werden.')
                ->modalSubmitActionLabel('Ja, alle Logs löschen')
                ->modalIcon(Heroicon::OutlinedExclamationTriangle)
                ->action(function () {
                    try {
                        $count = TheTVDBApiLog::query()->count();
                        TheTVDBApiLog::query()->delete();

                        Notification::make()
                            ->title('Logs cleared successfully')
                            ->body("Successfully deleted {$count} API log entries.")
                            ->success()
                            ->send();

                        // Reload statistics after clearing
                        $this->loadStatistics();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Error clearing logs')
                            ->body('Failed to delete API logs: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

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

