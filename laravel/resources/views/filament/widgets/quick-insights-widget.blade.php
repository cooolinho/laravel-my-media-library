<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-sparkles" class="w-5 h-5 text-amber-500"/>
                <span class="text-lg font-bold">Schnelle Insights</span>
            </div>
        </x-slot>

        @php
            $insights = $this->getInsights();
        @endphp

        <div class="quick-insights-container">
            <!-- Completion Insights -->
            <div class="insight-card insight-card-primary">
                <div class="insight-icon">
                    <x-filament::icon icon="heroicon-o-check-circle" class="w-8 h-8"/>
                </div>
                <div class="insight-content">
                    <div class="insight-value">{{ $insights['completeSeries'] }} / {{ $insights['totalSeries'] }}</div>
                    <div class="insight-label">Serien vollständig</div>
                    <div class="insight-progress">
                        <div class="insight-progress-bar" style="width: {{ $insights['completionRate'] }}%"></div>
                    </div>
                    <div class="insight-percentage">{{ $insights['completionRate'] }}% Vervollständigung</div>
                </div>
            </div>

            <!-- Episode Ownership -->
            <div class="insight-card insight-card-success">
                <div class="insight-icon">
                    <x-filament::icon icon="heroicon-o-video-camera" class="w-8 h-8"/>
                </div>
                <div class="insight-content">
                    <div class="insight-value">{{ $insights['ownedPercentage'] }}%</div>
                    <div class="insight-label">Episoden in Besitz</div>
                    <div class="insight-description">
                        Überdurchschnittlich
                        @if($insights['ownedPercentage'] >= 80)
                            <span class="text-green-600 dark:text-green-400">✓ Sehr gut!</span>
                        @elseif($insights['ownedPercentage'] >= 50)
                            <span class="text-yellow-600 dark:text-yellow-400">⚠ Gut</span>
                        @else
                            <span class="text-red-600 dark:text-red-400">⚡ Verbesserungsbedarf</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Jobs Status -->
            <div class="insight-card insight-card-info">
                <div class="insight-icon">
                    <x-filament::icon icon="heroicon-o-queue-list" class="w-8 h-8"/>
                </div>
                <div class="insight-content">
                    <div class="insight-value">{{ $insights['jobsInQueue'] }}</div>
                    <div class="insight-label">Jobs in Warteschlange</div>
                    <div class="insight-description">
                        @if($insights['failedJobsRate'] > 0)
                            <span class="text-yellow-600 dark:text-yellow-400">{{ $insights['failedJobsRate'] }}% mit Wiederholungen</span>
                        @else
                            <span class="text-green-600 dark:text-green-400">Keine fehlgeschlagenen Jobs</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- API Performance -->
            <div class="insight-card insight-card-warning">
                <div class="insight-icon">
                    <x-filament::icon icon="heroicon-o-bolt" class="w-8 h-8"/>
                </div>
                <div class="insight-content">
                    <div class="insight-value">{{ $insights['apiCacheRate'] }}%</div>
                    <div class="insight-label">API Cache-Trefferquote</div>
                    <div class="insight-description">
                        {{ $insights['apiCallsLast24h'] }} Aufrufe (24h)
                        @if($insights['apiCacheRate'] >= 70)
                            <span class="text-green-600 dark:text-green-400">✓ Optimal</span>
                        @elseif($insights['apiCacheRate'] >= 40)
                            <span class="text-yellow-600 dark:text-yellow-400">⚠ OK</span>
                        @else
                            <span class="text-red-600 dark:text-red-400">⚡ Niedrig</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Series -->
            @if($insights['topSeriesName'])
                <div class="insight-card insight-card-accent">
                    <div class="insight-icon">
                        <x-filament::icon icon="heroicon-o-trophy" class="w-8 h-8"/>
                    </div>
                    <div class="insight-content">
                        <div class="insight-value">{{ $insights['topSeriesEpisodes'] }} Episoden</div>
                        <div class="insight-label">Größte Serie</div>
                        <div class="insight-description font-semibold">
                            {{ $insights['topSeriesName'] }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Most Missing Series -->
            @if($insights['mostMissingSeriesName'])
                <div class="insight-card insight-card-danger">
                    <div class="insight-icon">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-8 h-8"/>
                    </div>
                    <div class="insight-content">
                        <div class="insight-value">{{ $insights['mostMissingCount'] }} fehlend</div>
                        <div class="insight-label">Meiste fehlende Episoden</div>
                        <div class="insight-description font-semibold">
                            {{ $insights['mostMissingSeriesName'] }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

