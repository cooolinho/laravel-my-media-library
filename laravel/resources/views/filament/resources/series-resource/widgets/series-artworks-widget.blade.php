<x-filament-widgets::widget>
    <x-filament::section>
        @if(count($artworks) <= 0)
            Noch keine Artwork geladen
        @else
            <div class="gallery-container">
                @foreach($artworks as $artwork)
                    <div class="gallery-item">
                        <a href="{{ $artwork[\App\Models\Artwork::image] }}" target="_blank">
                            <img src="{{ $artwork[\App\Models\Artwork::thumbnail] }}" alt="{{ $series->name . ' Artwork  ' . $artwork[\App\Models\Artwork::theTvDbId] }}">
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
