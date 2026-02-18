@php
    use App\Models\WarezLink;
@endphp

<x-filament-widgets::widget x-data="{ activeTab: 'tab1' }">
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
                <span class="flex items-center gap-2">
                    @if($link->getLogoUrl())
                        <img src="{{ $link->getLogoUrl() }}" alt="{{ $link[WarezLink::title] }}"
                             class="w-4 h-4 object-contain">
                    @endif
                    <span>{{ $link[WarezLink::title] }}</span>
                </span>
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    <div class="p-4">
        @foreach($links as $key => $link)
            <div x-show="activeTab === 'tab{{ $key + 1 }}'" x-cloak>
                <iframe src="{{ $link->getIframeUrl($series) }}" class="w-full h-[500px] border-0"></iframe>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
