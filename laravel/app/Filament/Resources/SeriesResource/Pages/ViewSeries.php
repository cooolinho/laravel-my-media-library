<?php

namespace App\Filament\Resources\SeriesResource\Pages;

use App\Config\FilesystemEnum;
use App\Filament\Resources\SeriesResource;
use App\Jobs\SyncEpisodesOwnedFromFileJob;
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
        ];
    }


    public function processFile(string $fileName): void
    {
        SyncEpisodesOwnedFromFileJob::dispatch(
            $this->record,
            Storage::disk(FilesystemEnum::DISK_PUBLIC->value)->path($fileName)
        );
    }
}
