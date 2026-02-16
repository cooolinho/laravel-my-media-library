<x-filament-panels::page>
    <div class="view-series-container">
        {{-- Hero Section mit Serien-Cover --}}
        <div class="series-hero">
            <div class="series-hero-backdrop">
                @if($record->data?->image)
                    <img src="{{ $record->data->image }}" alt="{{ $record->data->name ?? $record->name }}"
                         class="backdrop-image">
                    <div class="backdrop-overlay"></div>
                @endif
            </div>

            <div class="series-hero-content">
                <div class="series-poster">
                    @if($record->data?->image)
                        <img src="{{ $record->data->image }}" alt="{{ $record->data->name ?? $record->name }}">
                    @else
                        <div class="poster-placeholder">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14zM4 6h9v7H4zm10 0h6v2h-6zm0 3h6v2h-6zm0 3h6v2h-6z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="series-info">
                    <h1 class="series-title">{{ $record->data->getName() ?? $record->name }}</h1>

                    <div class="series-meta">
                        @if($record->data?->year)
                            <span class="meta-item">
                            <svg class="meta-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                            </svg>
                            {{ $record->data->year }}
                        </span>
                        @endif

                        @if($record->data?->status)
                            <span class="meta-item status-badge status-{{ strtolower($record->data->status) }}">
                            {{ $record->data->status }}
                        </span>
                        @endif

                        @if($record->data?->averageRuntime)
                            <span class="meta-item">
                            <svg class="meta-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                            </svg>
                            {{ $record->data->averageRuntime }} min
                        </span>
                        @endif

                        @if($record->data?->originalCountry)
                            <span class="meta-item">
                            <svg class="meta-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            {{ $record->data->originalCountry }}
                        </span>
                        @endif
                    </div>

                    @if($record->data?->score)
                        <div class="series-rating">
                            <div class="rating-circle">
                                <svg class="rating-svg" viewBox="0 0 36 36">
                                    <path class="rating-bg"
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none"
                                          stroke="rgba(255,255,255,0.1)"
                                          stroke-width="3"/>
                                    <path class="rating-progress"
                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none"
                                          stroke="url(#rating-gradient)"
                                          stroke-width="3"
                                          stroke-dasharray="{{ $record->data->score }}, 100"/>
                                    <defs>
                                        <linearGradient id="rating-gradient">
                                            <stop offset="0%" stop-color="#00ff88"/>
                                            <stop offset="100%" stop-color="#00cc88"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <div class="rating-text">
                                    <span class="rating-value">{{ number_format($record->data->score, 1) }}</span>
                                </div>
                            </div>
                            <span class="rating-label">Bewertung</span>
                        </div>
                    @endif

                    @if($record->data?->getOverview())
                        <p class="series-overview">{{ $record->data->getOverview() }}</p>
                    @endif

                    <div class="series-dates">
                        @if($record->data?->firstAired)
                            <div class="date-item">
                                <span class="date-label">Erste Ausstrahlung:</span>
                                <span class="date-value">{{ \Carbon\Carbon::parse($record->data->firstAired)->format('d.m.Y') }}</span>
                            </div>
                        @endif

                        @if($record->data?->lastAired)
                            <div class="date-item">
                                <span class="date-label">Letzte Ausstrahlung:</span>
                                <span class="date-value">{{ \Carbon\Carbon::parse($record->data->lastAired)->format('d.m.Y') }}</span>
                            </div>
                        @endif

                        @if($record->data?->nextAired)
                            <div class="date-item">
                                <span class="date-label">Nächste Ausstrahlung:</span>
                                <span class="date-value">{{ \Carbon\Carbon::parse($record->data->nextAired)->format('d.m.Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiken --}}
        <div class="series-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9h-4v4h-2v-4H9V9h4V5h2v4h4v2z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $record->episodes->count() }}</div>
                    <div class="stat-label">Episoden gesamt</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon owned">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $record->getEpisodesOwnedCount() }}</div>
                    <div class="stat-label">Eigene Episoden</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon progress">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ number_format($record->getEpisodeOwnedPercentage(), 1) }}%</div>
                    <div class="stat-label">Fortschritt</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon seasons">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $record->episodes->pluck('seasonNumber')->unique()->count() }}</div>
                    <div class="stat-label">Staffeln</div>
                </div>
            </div>
        </div>

        {{-- Episoden nach Staffeln --}}
        <div class="episodes-section">
            <h2 class="section-title">
                <svg class="title-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z"/>
                </svg>
                Episoden
            </h2>

            @php
                $episodesBySeason = $record->episodes->groupBy('seasonNumber')->sortKeys();
            @endphp

            <div class="seasons-container">
                @foreach($episodesBySeason as $seasonNumber => $episodes)
                    @php
                        $ownedCount = $episodes->where('owned', true)->count();
                        $totalCount = $episodes->count();
                        $percentage = $totalCount > 0 ? ($ownedCount / $totalCount) * 100 : 0;
                    @endphp

                    <div class="season-block">
                        <button class="season-header season-toggle" type="button" onclick="toggleSeason(this)">
                            <div class="season-title-wrapper">
                                <div class="season-title-with-icon">
                                    <svg class="accordion-icon" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M7 10l5 5 5-5z"/>
                                    </svg>
                                    <h3 class="season-title">Staffel {{ $seasonNumber }}</h3>
                                </div>
                                <span class="season-count">{{ $totalCount }} {{ $totalCount === 1 ? 'Episode' : 'Episoden' }}</span>
                            </div>

                            <div class="season-progress-wrapper">
                                <div class="season-progress-bar">
                                    <div class="season-progress-fill" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="season-progress-text">{{ $ownedCount }}/{{ $totalCount }}</span>
                            </div>
                        </button>

                        <div class="episodes-grid season-content">
                            @foreach($episodes->sortBy('number') as $episode)
                                <div class="episode-card {{ $episode->owned ? 'owned' : 'not-owned' }}">
                                    <div class="episode-number">
                                        <span class="episode-label">{{ $episode->getIdentifier() }}</span>
                                        @if($episode->owned)
                                            <svg class="owned-icon" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                            </svg>
                                        @endif
                                    </div>

                                    @if($episode->data?->image)
                                        <div class="episode-thumbnail">
                                            <a href="{{ \App\Filament\Resources\Episodes\EpisodeResource::getUrl('view', ['record' => $episode->id]) }}">
                                                <img src="{{ $episode->data->image }}"
                                                     alt="{{ $episode->data->getName() ?? 'Episode ' . $episode->number }}">
                                                <div class="thumbnail-overlay"></div>
                                            </a>
                                        </div>
                                    @endif

                                    <div class="episode-info">
                                        @if($episode->data?->getName())
                                            <h4 class="episode-title">{{ $episode->data->getName() }}</h4>
                                        @else
                                            <h4 class="episode-title">Episode {{ $episode->number }}</h4>
                                        @endif

                                        <div class="episode-meta">
                                            @if($episode->data?->aired)
                                                <span class="episode-meta-item">
                                                <svg class="meta-icon-small" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($episode->data->aired)->format('d.m.Y') }}
                                            </span>
                                            @endif

                                            @if($episode->data?->runtime)
                                                <span class="episode-meta-item">
                                                <svg class="meta-icon-small" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                                </svg>
                                                {{ $episode->data->runtime }} min
                                            </span>
                                            @endif
                                        </div>

                                        @if($episode->data?->getOverview())
                                            <p class="episode-overview">{{ $episode->data->getOverview() }}</p>
                                        @endif

                                        @if($episode->notes)
                                            <div class="episode-notes">
                                                <svg class="notes-icon" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                                </svg>
                                                <span>{{ $episode->notes }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function toggleSeason(button) {
            const seasonBlock = button.closest('.season-block');
            const content = seasonBlock.querySelector('.season-content');

            seasonBlock.classList.toggle('open');

            if (seasonBlock.classList.contains('open')) {
                content.style.maxHeight = content.scrollHeight + 'px';
            } else {
                content.style.maxHeight = '0';
            }
        }

        // Öffne die erste Staffel standardmäßig
        document.addEventListener('DOMContentLoaded', function () {
            const firstSeason = document.querySelector('.season-block');
            if (firstSeason) {
                const firstButton = firstSeason.querySelector('.season-toggle');
                if (firstButton) {
                    toggleSeason(firstButton);
                }
            }
        });
    </script>
</x-filament-panels::page>
