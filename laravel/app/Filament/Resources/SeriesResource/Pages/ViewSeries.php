<?php

namespace App\Filament\Resources\SeriesResource\Pages;

use App\Config\FilesystemEnum;
use App\Filament\Resources\SeriesResource;
use App\Filament\Resources\SeriesResource\Widgets\SeriesJobsWidget;
use App\Filament\Resources\SeriesResource\Widgets\SeriesStatsWidget;
use App\Jobs\SeriesDataJob;
use App\Jobs\SeriesEpisodesJob;
use App\Jobs\SyncEpisodesOwnedFromFileJob;
use App\Models\Series;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewSeries extends ViewRecord
{
    protected static string $resource = SeriesResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('uploadFile')
                ->label('Datei zum Abgleich hochladen')
                ->modalContent(view('series.components.upload-file'))
                ->form([
                    FileUpload::make('file')
                        ->label('Wählen Sie eine Datei aus')
                        ->required()
                        ->acceptedFileTypes(['text/*'])
                        ->disk(FilesystemEnum::DISK_PUBLIC->value)
                ])
                ->action(function (array $data) {
                    $this->processFile($data['file']);
                }),
            Actions\Action::make('loadSeriesData')
                ->requiresConfirmation()
                ->action(fn (Series $series) => SeriesDataJob::dispatch($series)),
            Actions\Action::make('loadSeriesEpisodesData')
                ->requiresConfirmation()
                ->action(fn (Series $series) => SeriesEpisodesJob::dispatch($series))
        ];
    }


    public function processFile(string $fileName): void
    {
        SyncEpisodesOwnedFromFileJob::dispatch(
            $this->record,
            Storage::disk(FilesystemEnum::DISK_PUBLIC->value)->path($fileName)
        );
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SeriesStatsWidget::class,
        ];
    }
}
