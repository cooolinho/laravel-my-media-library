@props(['warezLink', 'series'])

@php
    $url = $warezLink->getIframeUrl($series);
    $logoUrl = $warezLink->getLogoUrl();
@endphp

<a
        href="{{ $url }}"
        target="_blank"
        rel="noopener noreferrer"
        class="warez-link-button"
        title="{{ $warezLink->title }}"
>
    @if($logoUrl)
        <img
                src="{{ $logoUrl }}"
                alt="{{ $warezLink->title }}"
                class="warez-link-button__logo"
        >
    @endif
    <span class="warez-link-button__title">{{ $warezLink->title }}</span>
</a>

