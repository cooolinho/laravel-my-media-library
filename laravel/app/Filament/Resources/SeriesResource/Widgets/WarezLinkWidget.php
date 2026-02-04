<?php

namespace App\Filament\Resources\SeriesResource\Widgets;

use App\Models\Series;
use App\Models\WarezLink;
use Filament\Widgets\Widget;

class WarezLinkWidget extends Widget
{
    public ?Series $record = null;

    protected static string $view = 'filament.resources.series-resource.widgets.warez-link-widget';
    public int | string | array $columnSpan = 2;

    public function getViewData(): array
    {
        return [
            'series' => $this->record,
            'links' => WarezLink::all(),
        ];
    }
}
