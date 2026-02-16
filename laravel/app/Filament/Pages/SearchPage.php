<?php

namespace App\Filament\Pages;

use App\Contracts\TheTVDBSchema\SearchResult;
use App\Filament\Resources\Series\SeriesResource;
use App\Http\Client\TheTVDB\Api\SearchApi;
use App\Http\Client\TheTVDB\TheTVDBApiResponse;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchPage extends Page
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::MagnifyingGlass;
    protected static ?string $navigationLabel = 'Search'; // Name im Menü
    protected static ?int $navigationSort = 2; // Sortierung im Menü

    protected string $view = 'filament.pages.search';

    public string $query = '';
    public int $page = 1;
    public int $pageSize = 5;

    public array $pageOptions = [5, 10, 25, 50];
    public int $totalItems = 0;
    public int $totalPages = 1;
    public array $searchResults = [];
    private ?SearchApi $api = null;

    /**
     * @param SearchApi $api
     * @return void
     */
    public function boot(SearchApi $api): void
    {
        $this->api = $api;
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
                ]),
            Section::make('Actions')
                ->heading(false)
                ->schema($this->getFormActions()),
        ];
    }

    /**
     * @return array
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('search')
                ->action('submit')
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
        if (!$this->api) {
            return;
        }

        if ($this->query === '' || strlen($this->query) < 3) {
            $this->searchResults = [];
            return;
        }

        // first page in theTvDb is 0 (zero)
        $page = max(0, $this->page - 1);

        // search with query
        $result = $this->api->getSearchResults(
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

        return SeriesResource::getUrl('create', $actionNewParams);
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
     * @param TheTVDBApiResponse $result
     * @return void
     */
    private function setResultData(TheTVDBApiResponse $result): void
    {
        $this->searchResults = [];
        foreach ($result->getData() as $data) {
            $this->searchResults[] = (new SearchResult($data))->toArray();
        }

        $this->totalItems = $result->getTotalItems();
        $this->totalPages = $result->getTotalPages();
    }
}
