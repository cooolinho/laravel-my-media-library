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

