<?php

namespace App\Filament\Resources\Series\Pages;

use App\Filament\Resources\Series\Actions\DownloadArtworksAsZipAction;
use App\Filament\Resources\Series\Actions\LoadSeriesArtworksAction;
use App\Filament\Resources\Series\Actions\LoadSeriesDataAction;
use App\Filament\Resources\Series\Actions\LoadSeriesEpisodesDataAction;
use App\Filament\Resources\Series\Actions\UploadFileAction;
use App\Filament\Resources\Series\SeriesResource;
use App\Models\Artwork;
use App\Models\Series;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class ViewSeries extends ViewRecord
{
    public Model|Series|int|string|null $record;
    protected static string $resource = SeriesResource::class;
    protected string $view = 'series.view-series-detail';
    protected ?string $heading = '';

    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
            'artworksByType' => $this->getArtworksByType(),
        ];
    }

    protected function getArtworksByType()
    {
        return $this->record->artworks->groupBy(Artwork::type)->sortKeys();
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                EditAction::make(),
                UploadFileAction::make()
                    ->after(function () {
                        sleep(5); // Kurze Verzögerung, um sicherzustellen, dass der Job gestartet ist
                        $this->redirect(SeriesResource::getUrl('view', ['record' => $this->record]));
                    }),
                DownloadArtworksAsZipAction::make(),
                LoadSeriesDataAction::make(),
                LoadSeriesEpisodesDataAction::make(),
                LoadSeriesArtworksAction::make(),
            ])
                ->button()
                ->label('Aktionen')
                ->icon(Heroicon::Cog)
        ];
    }
}
