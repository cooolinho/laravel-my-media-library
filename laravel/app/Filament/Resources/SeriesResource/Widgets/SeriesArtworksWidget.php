<?php

namespace App\Filament\Resources\SeriesResource\Widgets;

use App\Models\Artwork;
use App\Models\Series;
use Filament\Widgets\Widget;

class SeriesArtworksWidget extends Widget
{
    public ?Series $record = null;
    public int | string | array $columnSpan = 2;

    protected static string $view = 'filament.resources.series-resource.widgets.series-artworks-widget';

    public function getViewData(): array
    {
        /** @var Series $series */
        $series = $this->record;
        return [
            'series' => $series,
            'artworks' => $series->artworks->sortBy(Artwork::type)->toArray(),
        ];
    }
}
