{{--
    Serien-Kachel Komponente

    Zeigt eine Serie in einer Kachelansicht mit:
    - Cover-Bild (Aspect Ratio 2:3)
    - Serientitel
    - Episodenanzahl
    - Fortschrittsbalken
    - Completion Badge (bei 100%)
    - Hover-Overlay mit Quick-Actions

    @props Series $series - Die anzuzeigende Serie
--}}
@props(['series'])

@php
    /** @var \App\Models\Series $series */
    // Series::has_one_data . '.' . SeriesData::image
    $coverImage = $series->data?->image;
    $coverUrl = $coverImage ?? null;
    $episodePercentage = $series->getEpisodeOwnedPercentage();
    $isComplete = $series->episodesComplete();
@endphp

<div class="series-card">
    <a href="{{ \App\Filament\Resources\Series\SeriesResource::getUrl('view', ['record' => $series]) }}"
       class="series-card__link">

        <!-- Cover Image -->
        <div class="series-card__cover">
            @if($coverUrl)
                <img src="{{ $coverUrl }}"
                     alt="{{ $series->name }}"
                     class="series-card__cover-image"
                     loading="lazy">
            @else
                <div class="series-card__placeholder">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                    </svg>
                    <p class="series-card__placeholder-text">Kein Cover</p>
                </div>
            @endif

            <!-- Completion Badge -->
            @if($isComplete)
                <div class="series-card__badge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            @endif

            <!-- Progress Bar -->
            <div class="series-card__progress">
                <div class="series-card__progress-fill" style="width: {{ $episodePercentage }}%"></div>
            </div>
        </div>

        <!-- Serie Info -->
        <div class="series-card__info">
            <h3 class="series-card__title" title="{{ $series->name }}">
                {{ $series->name }}
            </h3>
            <div class="series-card__meta">
                <span class="series-card__episodes">{{ $series->episodes->count() }} Episoden</span>
                <span class="series-card__percentage {{ $isComplete ? 'series-card__percentage--complete' : '' }}">
                    {{ $episodePercentage }}%
                </span>
            </div>
        </div>
    </a>

    <!-- Quick Actions Overlay -->
    <div class="series-card__overlay">
        <a href="{{ \App\Filament\Resources\Series\SeriesResource::getUrl('view', ['record' => $series]) }}"
           class="series-card__action"
           title="Anzeigen">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </a>
        <a href="{{ \App\Filament\Resources\Series\SeriesResource::getUrl('edit', ['record' => $series]) }}"
           class="series-card__action"
           title="Bearbeiten">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </a>
    </div>
</div>

