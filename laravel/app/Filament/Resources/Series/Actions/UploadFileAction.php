<?php

namespace App\Filament\Resources\Series\Actions;

use App\Config\FilesystemEnum;
use App\Jobs\SyncEpisodesOwnedFromFileJob;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class UploadFileAction
{
    public static function make(?string $name = null): Action
    {
        return Action::make($name ?? 'uploadFile')
            ->icon(Heroicon::DocumentCheck)
            ->label('Episoden abgleichen')
            ->modalContent(view('series.components.upload-file'))
            ->color(Color::Teal)
            ->schema([
                FileUpload::make('file')
                    ->label('Wählen Sie eine Datei aus')
                    ->required()
                    ->acceptedFileTypes(['text/*'])
                    ->disk(FilesystemEnum::DISK_PUBLIC->value)
            ])
            ->action(function (Series $record, array $data): void {
                $fileName = $data['file'];
                SyncEpisodesOwnedFromFileJob::dispatch(
                    $record,
                    Storage::disk(FilesystemEnum::DISK_PUBLIC->value)->path($fileName)
                );
            });
    }
}

