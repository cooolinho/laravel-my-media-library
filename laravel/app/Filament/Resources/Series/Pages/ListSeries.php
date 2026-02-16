<?php

namespace App\Filament\Resources\Series\Pages;

use App\Config\FilesystemEnum;
use App\Filament\Resources\Series\SeriesResource;
use App\Jobs\SyncAllEpisodesOwnedFromFileJob;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ListSeries extends ListRecords
{
    protected static string $resource = SeriesResource::class;

    protected string $view = 'series.list-series-grid';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('uploadFile')
                ->label('Datei zum Abgleich hochladen')
                ->modalContent(view('series.components.upload-file'))
                ->schema([
                    FileUpload::make('file')
                        ->label('Wählen Sie eine Datei aus')
                        ->required()
                        ->acceptedFileTypes(['text/*'])
                        ->disk(FilesystemEnum::DISK_PUBLIC->value)
                ])
                ->action(function (array $data) {
                    $this->processFile($data['file']);
                }),
            Action::make('uploadMultipleSeries')
                ->label('Serien importieren')
                ->modalContent(view('series.components.import-series'))
                ->schema([
                    Textarea::make('text')
                        ->label("Eingabe")
                        ->required()
                ])
                ->action(function (array $data) {
                    $this->processText($data['text']);
                }),
        ];
    }

    public function getFilteredTableQuery(): Builder
    {
        // Nutze table() um die gefilterte Query mit allen Modifikatoren zu erhalten
        return $this->table($this->getTable())->getQuery();
    }

    public function processFile(string $fileName): void
    {
        SyncAllEpisodesOwnedFromFileJob::dispatch(Storage::disk(FilesystemEnum::DISK_PUBLIC->value)->path($fileName));
    }

    public function processText(string $text): void
    {
        $lines = explode(PHP_EOL , trim($text));

        $series = [];
        foreach ($lines as $line) {
            preg_match_all('/^(.*)\s*-\s*(\d+)$/m', $line, $matches, PREG_SET_ORDER);
            if (empty($matches)) {
                continue;
            }

            $name = trim($matches[0][1] ?? '');
            $tvDbId = trim($matches[0][2] ?? '');

            if (isset($name, $tvDbId)) {
                $series[$tvDbId] = $name;
            }
        }

        // iterate and create series to trigger observer
        foreach ($series as $tvDbId => $name) {
            Series::firstOrCreate([
                Series::theTvDbId => $tvDbId,
                Series::name => $name,
            ]);
        }
    }
}
