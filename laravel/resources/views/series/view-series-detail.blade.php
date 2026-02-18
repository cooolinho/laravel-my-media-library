<x-filament-panels::page>
    <div class="view-series-container">
        {{-- Hero Section --}}
        @include('series.components.hero-section', ['record' => $record])

        {{-- Statistics Section --}}
        @include('series.components.stats-section', ['record' => $record])

        {{-- Episodes Section --}}
        @include('series.components.episodes-section', ['record' => $record])

        {{-- Artworks Section --}}
        @include('series.components.artworks-section', ['artworksByType' => $artworksByType])
    </div>

    {{-- JavaScript --}}
    @include('series.components.scripts')
</x-filament-panels::page>
