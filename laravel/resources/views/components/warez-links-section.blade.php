@props(['series'])

@php
    use App\Models\WarezLink;
    $warezLinks = WarezLink::active()->get();
@endphp

@if($warezLinks->isNotEmpty())
    <section class="warez-links-section">
        {{--        <h2 class="warez-links-section__title">Verfügbare Streaming-Links</h2>--}}

        <div class="warez-links-container">
            @foreach($warezLinks as $warezLink)
                <x-warez-link-button :warezLink="$warezLink" :series="$series"/>
            @endforeach
        </div>
    </section>
@endif

