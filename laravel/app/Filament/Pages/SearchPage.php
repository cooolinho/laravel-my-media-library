<?php

namespace App\Filament\Pages;

use App\Contracts\TheTVDBSchema\SearchResult;
use App\Http\Client\TheTVDB\ApiResponse;
use App\Http\Client\TheTVDB\TheTVDBApi;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchPage extends Page implements HasForms
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'Suche'; // Name im Menü
    protected static ?int $navigationSort = 2; // Sortierung im Menü

    protected static string $view = 'filament.pages.search';

    public string $query = '';
    public int $page = 1;
    public int $pageSize = 5;

    public array $pageOptions = [5, 10, 25, 50];
    public int $totalItems = 0;
    public int $totalPages = 1;
    public array $searchResults = [];
    private ?TheTVDBApi $api = null;

    /**
     * @param TheTVDBApi $api
     * @return void
     */
    public function boot(TheTVDBApi $api): void
    {
        $this->theTVDBApiService = $api;
    }

    /**
     * @return array
     */
    protected function getFormSchema(): array
    {
        return [
            Section::make('Suche')
                ->schema([
                    TextInput::make('query')
                        ->label(false)
                ])
        ];
    }

    /**
     * @return array
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('search')
                ->submit('submit')
                ->label('Suche starten'),
        ];
    }

    /**
     * @return void
     */
    public function submit(): void
    {
        $data = $this->form->getState();
        $this->query = $data['query'];

        $this->resetSearch();
        $this->updateSearch();
    }

    /**
     * @return void
     */
    public function nextPage(): void
    {
        $this->page++;
    }

    /**
     * @param int $page
     * @return void
     */
    public function gotoPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return void
     */
    public function previousPage(): void
    {
        $this->page--;
    }

    /**
     * @return void
     */
    public function updateSearch(): void
    {
        if (!$this->theTVDBApiService) {
            return;
        }

        if ($this->query === '' || strlen($this->query) < 3) {
            $this->searchResults = [];
            return;
        }

        // first page in theTvDb is 0 (zero)
        $page = max(0, $this->page - 1);

        // search with query
        $result = $this->theTVDBApiService->search(
            $this->query,
            $page,
            $this->pageSize,
        );

        // set result data
        $this->setResultData($result);
    }

    /**
     * @return View
     */
    public function render(): View
    {
        $this->updateSearch();

        return parent::render();
    }

    /**
     * @param string $name
     * @param string $theTvDbId
     * @return string
     */
    public function createSeriesUrl(string $name, string $theTvDbId): string
    {
        $actionNewParams = [
            Series::name => $name,
            Series::theTvDbId => $theTvDbId,
        ];

        return url('/admin/series/create?' . http_build_query($actionNewParams));
    }

    /**
     * @return LengthAwarePaginator
     */
    public function getPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator($this->searchResults, $this->totalItems, $this->pageSize, $this->page);
    }

    /**
     * @return void
     */
    private function resetSearch(): void
    {
        $this->reset(
            'page',
            'pageSize',
            'totalItems',
            'totalPages',
            'searchResults',
        );
    }

    /**
     * @param ApiResponse $result
     * @return void
     */
    private function setResultData(ApiResponse $result): void
    {
        $this->searchResults = [];
        foreach ($result->getData() as $data) {
            $this->searchResults[] = (new SearchResult($data))->toArray();
        }

        $this->totalItems = $result->getTotalItems();
        $this->totalPages = $result->getTotalPages();
    }
}
