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

