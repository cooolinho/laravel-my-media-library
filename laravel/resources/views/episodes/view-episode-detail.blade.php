<x-filament-panels::page>
    <div class="view-episode-container">
        {{-- Episode Hero Section --}}
        <div class="episode-hero">
            <div class="episode-hero-backdrop">
                @if($record->data?->image)
                    <img src="{{ $record->data->image }}" alt="{{ $record->data->name ?? $record->getIdentifier() }}"
                         class="backdrop-image">
                    <div class="backdrop-overlay"></div>
                @elseif($record->series?->data?->image)
                    <img src="{{ $record->series->data->image }}" alt="{{ $record->series->name }}"
                         class="backdrop-image">
                    <div class="backdrop-overlay"></div>
                @endif
            </div>

            <div class="episode-hero-content">
                {{-- Episode Thumbnail --}}
                <div class="episode-thumbnail-large">
                    @if($record->data?->image)
                        <img src="{{ $record->data->image }}"
                             alt="{{ $record->data->name ?? $record->getIdentifier() }}">
                    @else
                        <div class="thumbnail-placeholder">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z"/>
                            </svg>
                            <span class="episode-identifier">{{ $record->getIdentifier() }}</span>
                        </div>
                    @endif

                    @if($record->owned)
                        <div class="owned-badge">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                            </svg>
                            <span>In Besitz</span>
                        </div>
                    @endif
                </div>

                {{-- Episode Info --}}
                <div class="episode-main-info">
                    {{-- Breadcrumb / Series Link --}}
                    @if($record->series)
                        <div class="series-breadcrumb">
                            <a href="{{ route('filament.admin.resources.series.view', $record->series) }}"
                               class="series-link">
                                <svg class="breadcrumb-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                </svg>
                                <span>{{ $record->series->data->name ?? $record->series->name }}</span>
                            </a>
                            <svg class="separator-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                            </svg>
                            <span class="current-episode">{{ $record->getIdentifier() }}</span>
                        </div>
                    @endif

                    <h1 class="episode-title">
                        @if($record->data?->name)
                            {{ $record->data->name }}
                        @else
                            Episode {{ $record->number }}
                        @endif
                    </h1>

                    <div class="episode-identifier-badge">
                        <span class="badge-label">Episode</span>
                        <span class="badge-value">{{ $record->getIdentifier() }}</span>
                    </div>

                    {{-- Episode Meta --}}
                    <div class="episode-meta-grid">
                        @if($record->seasonNumber)
                            <div class="meta-card">
                                <svg class="meta-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
                                </svg>
                                <div class="meta-content">
                                    <span class="meta-label">Staffel</span>
                                    <span class="meta-value">{{ $record->seasonNumber }}</span>
                                </div>
                            </div>
                        @endif

                        @if($record->number)
                            <div class="meta-card">
                                <svg class="meta-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                                <div class="meta-content">
                                    <span class="meta-label">Episode</span>
                                    <span class="meta-value">{{ $record->number }}</span>
                                </div>
                            </div>
                        @endif

                        @if($record->data?->aired)
                            <div class="meta-card">
                                <svg class="meta-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                </svg>
                                <div class="meta-content">
                                    <span class="meta-label">Ausgestrahlt</span>
                                    <span class="meta-value">{{ \Carbon\Carbon::parse($record->data->aired)->format('d.m.Y') }}</span>
                                </div>
                            </div>
                        @endif

                        @if($record->data?->runtime)
                            <div class="meta-card">
                                <svg class="meta-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                </svg>
                                <div class="meta-content">
                                    <span class="meta-label">Laufzeit</span>
                                    <span class="meta-value">{{ $record->data->runtime }} min</span>
                                </div>
                            </div>
                        @endif

                        @if($record->data?->year)
                            <div class="meta-card">
                                <svg class="meta-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/>
                                </svg>
                                <div class="meta-content">
                                    <span class="meta-label">Jahr</span>
                                    <span class="meta-value">{{ $record->data->year }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="meta-card {{ $record->owned ? 'owned' : 'not-owned' }}">
                            <svg class="meta-icon" viewBox="0 0 24 24" fill="currentColor">
                                @if($record->owned)
                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                @else
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                @endif
                            </svg>
                            <div class="meta-content">
                                <span class="meta-label">Status</span>
                                <span class="meta-value">{{ $record->owned ? 'In Besitz' : 'Nicht vorhanden' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Episode Overview --}}
        @if($record->data?->overview)
            <div class="episode-section">
                <div class="section-header">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                    <h2 class="section-title">Übersicht</h2>
                </div>

                <div class="overview-content">
                    <p class="overview-text">{{ $record->data->overview }}</p>
                </div>
            </div>
        @endif

        {{-- Notes Section --}}
        @if($record->notes)
            <div class="episode-section">
                <div class="section-header">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 9h12v2H6V9zm8 5H6v-2h8v2zm4-6H6V6h12v2z"/>
                    </svg>
                    <h2 class="section-title">Notizen</h2>
                </div>

                <div class="notes-content">
                    <p class="notes-text">{{ $record->notes }}</p>
                </div>
            </div>
        @endif

        {{-- Series Information Card --}}
        @if($record->series)
            <div class="episode-section">
                <div class="section-header">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9h-4v4h-2v-4H9V9h4V5h2v4h4v2z"/>
                    </svg>
                    <h2 class="section-title">Serie</h2>
                </div>

                <div class="series-info-card">
                    <div class="series-poster-small">
                        @if($record->series->data?->image)
                            <img src="{{ $record->series->data->image }}"
                                 alt="{{ $record->series->data->name ?? $record->series->name }}">
                        @else
                            <div class="poster-placeholder-small">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="series-details">
                        <h3 class="series-name">{{ $record->series->data->name ?? $record->series->name }}</h3>

                        @if($record->series->data?->overview)
                            <p class="series-overview">{{ Str::limit($record->series->data->overview, 200) }}</p>
                        @endif

                        <div class="series-meta-row">
                            @if($record->series->data?->year)
                                <span class="series-meta-item">
                                <svg class="series-meta-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                </svg>
                                {{ $record->series->data->year }}
                            </span>
                            @endif

                            @if($record->series->data?->status)
                                <span class="series-meta-item status-badge-small status-{{ strtolower($record->series->data->status) }}">
                                {{ $record->series->data->status }}
                            </span>
                            @endif

                            @if($record->series->episodes)
                                <span class="series-meta-item">
                                <svg class="series-meta-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9h-4v4h-2v-4H9V9h4V5h2v4h4v2z"/>
                                </svg>
                                {{ $record->series->episodes->count() }} Episoden
                            </span>
                            @endif
                        </div>

                        <a href="{{ route('filament.admin.resources.series.view', $record->series) }}"
                           class="series-view-button">
                            <span>Serie anzeigen</span>
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Related Episodes (Same Season) --}}
        @if($record->series)
            @php
                $relatedEpisodes = $record->series->episodes()
                    ->where('seasonNumber', $record->seasonNumber)
                    ->where('id', '!=', $record->id)
                    ->orderBy('number')
                    ->limit(6)
                    ->get();
            @endphp

            @if($relatedEpisodes->count() > 0)
                <div class="episode-section">
                    <div class="section-header">
                        <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z"/>
                        </svg>
                        <h2 class="section-title">Weitere Episoden aus Staffel {{ $record->seasonNumber }}</h2>
                    </div>

                    <div class="related-episodes-grid">
                        @foreach($relatedEpisodes as $episode)
                            <a href="{{ route('filament.admin.resources.episodes.view', $episode) }}"
                               class="related-episode-card">
                                @if($episode->data?->image)
                                    <div class="related-episode-thumbnail">
                                        <img src="{{ $episode->data->image }}"
                                             alt="{{ $episode->data->name ?? $episode->getIdentifier() }}">
                                        @if($episode->owned)
                                            <div class="owned-indicator">
                                                <svg viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="related-episode-info">
                                    <span class="related-episode-number">{{ $episode->getIdentifier() }}</span>
                                    <h4 class="related-episode-title">
                                        {{ $episode->data->name ?? 'Episode ' . $episode->number }}
                                    </h4>
                                    @if($episode->data?->aired)
                                        <span class="related-episode-date">
                                        {{ \Carbon\Carbon::parse($episode->data->aired)->format('d.m.Y') }}
                                    </span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-filament-panels::page>
