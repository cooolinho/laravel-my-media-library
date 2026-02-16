<?php

namespace App\Filament\Resources\Series\Pages;

use App\Config\FilesystemEnum;
use App\Filament\Resources\Series\SeriesResource;
use App\Jobs\SeriesArtworkJob;
use App\Jobs\SeriesDataJob;
use App\Jobs\SeriesEpisodesJob;
use App\Jobs\SyncEpisodesOwnedFromFileJob;
use App\Models\Artwork;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ViewSeries extends ViewRecord
{
    public Model|Series|int|string|null $record;
    protected static string $resource = SeriesResource::class;
    protected string $view = 'series.view-series-detail';
    protected ?string $heading = '';

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                EditAction::make(),
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
                    })
                    ->after(function () {
                        sleep(5); // Kurze Verzögerung, um sicherzustellen, dass der Job gestartet ist
                        $this->redirect(SeriesResource::getUrl('view', ['record' => $this->record]));
                    }),
                Action::make('loadSeriesData')
                    ->requiresConfirmation()
                    ->action(fn(Series $series) => SeriesDataJob::dispatch($series)),
                Action::make('loadSeriesEpisodesData')
                    ->requiresConfirmation()
                    ->action(fn(Series $series) => SeriesEpisodesJob::dispatch($series)),
                Action::make('loadSeriesArtworks')
                    ->requiresConfirmation()
                    ->action(fn(Series $series) => SeriesArtworkJob::dispatch($series)),
                Action::make('loadArtworksAsZip')
                    ->requiresConfirmation()
                    ->action(fn(Series $series) => $this->downloadArtworks($series)),
            ])
                ->button()
                ->label('Aktionen')
                ->icon(Heroicon::Cog)
        ];
    }


    public function processFile(string $fileName): void
    {
        SyncEpisodesOwnedFromFileJob::dispatch(
            $this->record,
            Storage::disk(FilesystemEnum::DISK_PUBLIC->value)->path($fileName)
        );
    }

    public function downloadArtworks(Series $series): BinaryFileResponse
    {
        $artworks = $series->artworks->pluck(Artwork::image);

        $zipFileName = sprintf('artworks-%s.zip', $series->data->slug ?? $series->theTvDbId);
        $zipFilePath = storage_path("app/public/{$zipFileName}");

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($artworks as $imageUrl) {
                $imageContent = $this->downloadImage($imageUrl);
                if ($imageContent === false) {
                    continue;
                }

                $imageName = basename(parse_url($imageUrl, PHP_URL_PATH));
                $zip->addFromString($imageName, $imageContent);
            }
            $zip->close();
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    private function downloadImage($url): bool|string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode == 200) ? $data : false;
    }
}
