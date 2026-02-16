{{-- Warez Links Section --}}
@php
    $warezLinks = \App\Models\WarezLink::all();
@endphp

@if($warezLinks->count() > 0)
    <div class="warez-links-section">
        <h2 class="section-title">
            <svg class="title-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
            </svg>
            Externe Links
        </h2>

        <div class="warez-accordion-block">
            <button class="warez-accordion-header" type="button" onclick="toggleWarezLinks(this)">
                <div class="warez-title-wrapper">
                    <div class="warez-title-with-icon">
                        <svg class="accordion-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
                        <h2 class="section-title">
                            <svg class="title-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/>
                            </svg>
                            Externe Links
                        </h2>
                    </div>
                    <span class="warez-count">{{ $warezLinks->count() + 1 }} Links</span>
                </div>
            </button>

            <div class="warez-accordion-content">
                <div class="warez-tabs" x-data="{ activeTab: 'tvdb' }">
                    {{-- Tab Headers --}}
                    <div class="warez-tabs-header">
                        <button
                                @click="activeTab = 'tvdb'"
                                :class="{ 'active': activeTab === 'tvdb' }"
                                class="warez-tab-button"
                        >
                            <svg class="tab-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            TheTVDB
                        </button>

                        @foreach($warezLinks as $link)
                            <button
                                    @click="activeTab = 'link-{{ $link->id }}'"
                                    :class="{ 'active': activeTab === 'link-{{ $link->id }}' }"
                                    class="warez-tab-button"
                            >
                                <svg class="tab-icon" viewBox="0 0 24 24" fill="currentColor">
                                    @if($link->placeholderType === \App\Models\WarezLink::PLACEHOLDER_TVDB_ID)
                                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                    @else
                                        <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/>
                                    @endif
                                </svg>
                                {{ $link->title }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Tab Content --}}
                    <div class="warez-tabs-content">
                        @php
                            $theTvDbUrl = "https://thetvdb.com/series/" . ($record->data?->slug ?? $record->name);
                        @endphp
                        {{-- TheTVDB Tab --}}
                        <div x-show="activeTab === 'tvdb'" class="warez-tab-panel">
                            <div class="iframe-info">
                                <svg class="info-icon" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                                <span>TheTVDB</span>
                                <a href="{{ $theTvDbUrl }}" class="iframe-url"
                                   target="_blank">
                                    {{ $theTvDbUrl }}
                                </a>
                            </div>
                            <iframe
                                    src="{{ $theTvDbUrl }}"
                                    class="warez-iframe"
                            ></iframe>
                        </div>

                        {{-- Warez Links Tabs --}}
                        @foreach($warezLinks as $link)
                            <div x-show="activeTab === 'link-{{ $link->id }}'" class="warez-tab-panel">
                                <div class="iframe-info">
                                    <svg class="info-icon" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                    </svg>
                                    <span>{{ $link->title }}</span>
                                    <a href="{{ $link->getIframeUrl($record) }}" class="iframe-url"
                                       target="_blank">
                                        {{ $link->getIframeUrl($record) }}
                                    </a>
                                </div>
                                <iframe
                                        src="{{ $link->getIframeUrl($record) }}"
                                        class="warez-iframe"
                                ></iframe>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

