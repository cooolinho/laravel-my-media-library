{{-- Grid-Layout für Poster, ClearArt, ClearLogo etc. (mehrere nebeneinander) --}}
@php
    $config = \App\Helpers\ArtworkHelper::getTypeConfig($type);
    $gridClass = \App\Helpers\ArtworkHelper::getGridClass($type);
    $paddingBottom = \App\Helpers\ArtworkHelper::getPaddingBottom($type);
    $typeName = \App\Helpers\ArtworkHelper::getTypeName($type);
    $displayName = \App\Helpers\ArtworkHelper::getDisplayName($type);
@endphp

<div class="artworks-grid {{ $gridClass }} artwork-type-{{ $typeName }}">
    @foreach($artworks as $artwork)
        <div class="artwork-item">
            <div class="artwork-image-container" style="padding-bottom: {{ $paddingBottom }}%;">
                <img src="{{ $artwork->thumbnail }}"
                     alt="{{ $displayName }} artwork"
                     class="artwork-thumbnail"
                     loading="lazy">
                <div class="artwork-overlay">
                    <a href="{{ $artwork->image }}"
                       target="_blank"
                       class="artwork-view-button">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                        </svg>
                        Vollbild
                    </a>
                </div>
            </div>
            <div class="artwork-info">
                <span class="artwork-id">ID: {{ $artwork->theTvDbId }}</span>
            </div>
        </div>
    @endforeach
</div>

