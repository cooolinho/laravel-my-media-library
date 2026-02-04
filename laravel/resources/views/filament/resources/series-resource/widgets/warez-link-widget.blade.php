@php
    use App\Models\WarezLink;
@endphp

<x-filament-widgets::widget x-data="{ activeTab: 'tab0' }">
    <x-filament::tabs label="Content tabs">
        <x-filament::tabs.item
            alpine-active="activeTab === 'tab0'"
            x-on:click="activeTab = 'tab0'"
        >
            TheTVDB
        </x-filament::tabs.item>
        @foreach($links as $key => $link)
            <x-filament::tabs.item
                alpine-active="activeTab === 'tab{{ $key + 1 }}'"
                x-on:click="activeTab = 'tab{{ $key + 1 }}'"
            >
                {{ $link[WarezLink::title] }}
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    <div class="p-4">
        <div x-show="activeTab === 'tab0'" x-cloak>
            <iframe src="https://thetvdb.com/series/{{ $series->data?->slug ?? $series->name }}" frameborder="0" width="100%" height="500px"></iframe>
        </div>
        @foreach($links as $key => $link)
            <div x-show="activeTab === 'tab{{ $key + 1 }}'" x-cloak>
                <iframe src="{{ $link->getIframeUrl($series->name) }}" frameborder="0" width="100%" height="500px"></iframe>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
