<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-film" class="w-5 h-5"/>
                <span>Top Serien - Übersicht</span>
            </div>
        </x-slot>

        <div class="dashboard-series-grid">
            @foreach($this->getTopSeries() as $series)
                <div class="series-card">
                    <div class="series-card-header">
                        <h3 class="series-name">
                            <a href="{{ $series['url'] }}">
                                {{ $series['name'] }}
                            </a>
                        </h3>
                        <div class="series-stats">
                            <span class="stat-badge stat-badge-success">
                                {{ $series['owned'] }} Besessen
                            </span>
                            @if($series['missing'] > 0)
                                <span class="stat-badge stat-badge-warning">
                                    {{ $series['missing'] }} Fehlend
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="series-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $series['percentage'] }}%"></div>
                        </div>
                        <div class="progress-text">
                            <span>{{ $series['owned'] }} / {{ $series['total'] }} Episoden</span>
                            <span class="progress-percentage">{{ $series['percentage'] }}%</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>


