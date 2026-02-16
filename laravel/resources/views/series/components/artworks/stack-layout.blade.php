{{-- Stack-Layout für Banner und Backgrounds (untereinander) --}}
@php
    $paddingBottom = \App\Helpers\ArtworkHelper::getPaddingBottom($type);
    $typeName = \App\Helpers\ArtworkHelper::getTypeName($type);
    $displayName = \App\Helpers\ArtworkHelper::getDisplayName($type);
@endphp

<div class="artworks-stack artwork-type-{{ $typeName }}">
    @foreach($artworks as $artwork)
        <div class="artwork-item-stack">
            <div class="artwork-image-container-stack" style="padding-bottom: {{ $paddingBottom }}%;">
                <img src="{{ $artwork->thumbnail }}"
                     alt="{{ $displayName }} artwork"
                     class="artwork-thumbnail-stack"
                     loading="lazy">
                <div class="artwork-overlay-stack">
                    <div class="artwork-stack-info">
                        <span class="artwork-stack-id">ID: {{ $artwork->theTvDbId }}</span>
                        <a href="{{ $artwork->image }}"
                           target="_blank"
                           class="artwork-view-button-stack">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                            Vollbild ansehen
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

