<x-filament-panels::page>
    <div x-data="{ viewMode: localStorage.getItem('seriesViewMode') || 'grid' }"
         x-init="$watch('viewMode', value => localStorage.setItem('seriesViewMode', value))">

        <!-- Ansichts-Umschalter -->
        <div class="series-view-toggle">
            <div class="series-view-toggle__buttons">
                <button
                        @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'series-view-toggle__button series-view-toggle__button--active' : 'series-view-toggle__button'"
                        title="Kachelansicht">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span class="series-view-toggle__button-text">Kacheln</span>
                </button>
                <button
                        @click="viewMode = 'table'"
                        :class="viewMode === 'table' ? 'series-view-toggle__button series-view-toggle__button--active' : 'series-view-toggle__button'"
                        title="Tabellenansicht">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span class="series-view-toggle__button-text">Tabelle</span>
                </button>
            </div>
        </div>

        <!-- Tabelle (enthält Suche, Filter, Sortierung) -->
        <div x-show="viewMode === 'table'">
            {{ $this->table }}
        </div>

        <!-- Grid-Modus mit Ansichts-Umschalter -->
        <div x-show="viewMode === 'grid'">

            <!-- Grid-Kachelansicht -->
            @php
                $records = $this->getFilteredSortedTableQuery()->get();
            @endphp

            <div class="series-grid">
                @forelse($records as $series)
                    <x-series.series-card :series="$series"/>
                @empty
                    <div class="series-grid__empty">
                        <div class="series-grid__empty-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <h3 class="series-grid__empty-title">Keine Serien gefunden</h3>
                        <p class="series-grid__empty-description">Es wurden keine Serien gefunden, die Ihren
                            Suchkriterien entsprechen.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>

