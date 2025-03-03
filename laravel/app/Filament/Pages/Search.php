<?php

namespace App\Filament\Pages;

use App\Models\Series;
use App\Services\TheTVDBApiService;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class Search extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Suche'; // Name im Menü
    protected static ?int $navigationSort = 2; // Sortierung im Menü

    protected static string $view = 'filament.pages.search';
    public string $query = '';
    public int $page = 0;
    public int $pageSize = 0;
    public int $totalItems = 0;
    public int $totalPages = 1;
    public bool $hasLinkPrevious = false;
    public bool $hasLinkNext = false;
    public array $series = [];
    private ?TheTVDBApiService $theTVDBApiService = null;

    public function boot(TheTVDBApiService $theTVDBApiService): void
    {
        $this->theTVDBApiService = $theTVDBApiService;
    }

    public function increasePage(): void
    {
        $this->page++;
    }

    public function decreasePage(): void
    {
        $this->page--;
    }

    public function updateSearch(): void
    {
        if (!$this->theTVDBApiService) {
            return;
        }

        if ($this->query === '' || strlen($this->query) < 3) {
            $this->series = [];
            return;
        }

        $result = $this->theTVDBApiService->search($this->query, $this->page);
        $this->series = $result->getData();
        $this->totalItems = $result->getTotalItems();
        $this->pageSize = $result->getTotalItems();
        $this->totalPages = $result->getTotalPages();
        $this->hasLinkPrevious = $result->hasLinkPrevious();
        $this->hasLinkNext = $result->hasLinkNext();
    }

    public function render(): View
    {
        $this->updateSearch();

        return parent::render();
    }

    public function createSeriesUrl(string $name, string $theTvDbId): string
    {
        $actionNewParams = [
            Series::name => $name,
            Series::theTvDbId => $theTvDbId,
        ];

        return url('/admin/series/create?' . http_build_query($actionNewParams));
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'query') {
            $this->resetPages();
        }
    }

    public function resetPages(): void
    {
        $this->reset(
            'page',
            'pageSize',
            'totalPages',
            'totalItems',
            'hasLinkPrevious',
            'hasLinkNext',
            'series'
        );
    }
}
