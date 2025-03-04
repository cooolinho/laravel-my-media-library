@php
    use App\Contracts\TheTVDBSchema\SearchResult;
@endphp

<x-filament::page>
    @push('styles')
        @vite('resources/css/filament.css')
    @endpush

    <x-filament-panels::form wire:submit="submit">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <div class="p-6">
        <div id="searchResults" class="search-results">
                @foreach($searchResults as $searchResult)
                    <div class="result-card">
                        <img class="result-image" src="{{ $searchResult[SearchResult::image_url] }}" alt="">
                        <div class="result-info">
                            <h3 class="result-name">{{ $searchResult[SearchResult::name] }}</h3>
                            <p class="result-overview">{{ $searchResult[SearchResult::overview] }}</p>
                            <div class="result-details">
                                <span class="result-year">Jahr: {{ $searchResult[SearchResult::year] }}</span>
                                <span class="result-status">Status: {{ $searchResult[SearchResult::status] }}</span>
                                <span class="result-airtime">Erstausstrahlung: {{ $searchResult[SearchResult::first_air_time] }}</span>
                            </div>
                            <div class="result-actions">
                                <x-filament::link color="success" :href="$this->createSeriesUrl($searchResult[SearchResult::name], $searchResult[SearchResult::tvdb_id])">
                                    Hinzufügen
                                </x-filament::link>
                            </div>
                        </div>
                    </div>
                @endforeach
        </div>

        @if(count($searchResults) >0)
            <x-filament::pagination
                :paginator="$this->getPaginator()"
                :page-options="$pageOptions"
                :current-page-option-property="'pageSize'"
                :extreme-links="true"
            />
        @endif
    </div>
</x-filament::page>
