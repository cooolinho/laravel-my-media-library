<x-filament::page>
    @push('styles')
        @vite('resources/css/filament.css')
    @endpush

    <div class="p-6">
        <input
            type="text"
            id="searchInput"
            class="border p-2 rounded w-full"
            placeholder="Geben Sie mindestens 3 Buchstaben ein..."
            wire:model.live.debounce.1000ms="query"
{{--            wire:model="query"--}}
{{--            wire:change="queryChanged($el)"--}}
        />

        <div id="searchResults" class="search-results">
                @foreach($series as $data)
                    @php
                        $name = $data['translations']['deu'] ?? $data['translations']['eng'];
                    @endphp
                    <div class="result-card">
                        <img class="result-image" src="{{ $data['image_url'] ?? '' }}" alt="">
                        <div class="result-info">
                            <h3 class="result-name">{{ $name }}</h3>
                            <p class="result-overview">{{ $data['overview'] ?? '' }}</p>
                            <div class="result-details">
                                <span class="result-year">Jahr: {{ $data['year'] ?? '' }}</span>
                                <span class="result-status">Status: {{ $data['status'] ?? '' }}</span>
                                <span class="result-airtime">Erstausstrahlung: {{ $data['first_air_time'] ?? '' }}</span>
                            </div>
                            <div class="result-actions">
                                <x-filament::link color="success" :href="$this->createSeriesUrl($name, $data['tvdb_id'])">
                                    Hinzufügen
                                </x-filament::link>
                            </div>
                        </div>
                    </div>
                @endforeach
        </div>

        <x-filament::button wire:click="decreasePage" :disabled="$hasLinkPrevious === false">
            Vorherige Seite
        </x-filament::button>

        <x-filament::button>
            Seite {{ $page + 1 }} / {{ $totalPages }}
        </x-filament::button>

        <x-filament::button wire:click="increasePage" :disabled="$hasLinkNext === false">
            Nächste Seite
        </x-filament::button>
    </div>
</x-filament::page>
