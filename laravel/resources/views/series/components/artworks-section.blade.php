{{-- Artworks Section --}}
@if($artworksByType->count() > 0)
    <div class="artworks-section">
        <h2 class="section-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
            </svg>
            Artworks
        </h2>

        <div class="artworks-accordion-block">
            <button class="artworks-accordion-header" type="button" onclick="toggleArtworks(this)">
                <div class="artworks-title-wrapper">
                    <div class="artworks-title-with-icon">
                        <svg class="accordion-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
                        <span class="artworks-subtitle">Artworks nach Typ</span>
                    </div>
                    <span class="artworks-count">{{ $artworksByType->sum(fn($artworks) => $artworks->count()) }} Artworks in {{ $artworksByType->count() }} Kategorien</span>
                </div>
            </button>

            <div class="artworks-accordion-content">
                <div class="artworks-tabs" x-data="{ activeArtworkType: '{{ $artworksByType->keys()->first() }}' }">
                    {{-- Tab Headers --}}
                    <div class="artworks-tabs-header">
                        @foreach($artworksByType as $type => $artworks)
                            @php
                                $displayName = \App\Helpers\ArtworkHelper::getDisplayName($type);
                            @endphp
                            <button
                                    @click="activeArtworkType = '{{ $type }}'"
                                    :class="{ 'active': activeArtworkType === '{{ $type }}' }"
                                    class="artworks-tab-button"
                            >
                                <svg class="tab-icon" viewBox="0 0 24 24" fill="currentColor">
                                    @if($type === 'poster' || $type === 'season' || $type === 'series')
                                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.96-2.36L6.5 17h11l-3.54-4.71z"/>
                                    @elseif($type === 'background')
                                        <path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/>
                                    @elseif($type === 'banner')
                                        <path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14zM5 7h14v2H5z"/>
                                    @else
                                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                    @endif
                                </svg>
                                <span>{{ $displayName }}</span>
                                <span class="artwork-count-badge">{{ $artworks->count() }}</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Tab Content --}}
                    <div class="artworks-tabs-content">
                        @foreach($artworksByType as $type => $artworks)
                            <div x-show="activeArtworkType === '{{ $type }}'" class="artworks-tab-panel">
                                @php
                                    $displayName = \App\Helpers\ArtworkHelper::getDisplayName($type);
                                    $isStackLayout = \App\Helpers\ArtworkHelper::isStackLayout($type);
                                @endphp

                                <div class="artworks-layout-info">
                                    <svg class="info-icon" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                    </svg>
                                    <span>{{ $displayName }} - {{ $artworks->count() }} {{ $artworks->count() === 1 ? 'Bild' : 'Bilder' }}</span>
                                </div>

                                @if($isStackLayout)
                                    @include('series.components.artworks.stack-layout', ['type' => $type, 'artworks' => $artworks])
                                @else
                                    @include('series.components.artworks.grid-layout', ['type' => $type, 'artworks' => $artworks])
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

