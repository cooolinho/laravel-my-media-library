<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Info --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ $seriesName }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Abgleich durchgeführt am {{ $timestamp }}
                    </p>
                </div>
                <div>
                    <a href="{{ \App\Filament\Resources\Series\SeriesResource::getUrl('view', ['record' => $seriesId]) }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">
                        Zurück zur Serie
                    </a>
                </div>
            </div>
        </div>

        {{-- Matches --}}
        @foreach($matches as $match)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                {{-- File Name Header --}}
                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        📁 {{ $match['file_name'] }}
                    </h3>
                </div>

                {{-- Episode Matches --}}
                <div class="p-6">
                    @if(empty($match['matches']))
                        <div class="text-center py-8">
                            <div class="text-gray-400 dark:text-gray-500 text-5xl mb-3">🔍</div>
                            <p class="text-gray-500 dark:text-gray-400">
                                Keine passenden Episoden gefunden
                            </p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($match['matches'] as $episodeMatch)
                                @php
                                    $similarityColor = match(true) {
                                        $episodeMatch['similarity'] >= 80 => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        $episodeMatch['similarity'] >= 60 => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        default => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    };
                                @endphp

                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300">
                                                {{ $episodeMatch['identifier'] }}
                                            </span>

                                            @if($episodeMatch['owned'])
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                    ✓ Besitzt
                                                </span>
                                            @endif

                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium {{ $similarityColor }}">
                                                {{ $episodeMatch['similarity'] }}% Übereinstimmung
                                            </span>
                                        </div>

                                        <p class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $episodeMatch['title'] }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        {{ ($this->addNoteToEpisodeAction)(['fileName' => $match['file_name'], 'episodeId' => $episodeMatch['episode_id'], 'episodeTitle' => $episodeMatch['identifier'] . ' - ' . $episodeMatch['title']]) }}

                                        <a href="{{ \App\Filament\Resources\Episodes\EpisodeResource::getUrl('view', ['record' => $episodeMatch['episode_id']]) }}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-primary-700 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition"
                                           target="_blank">
                                            Episode öffnen →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Summary Statistics --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Zusammenfassung
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ count($matches) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Dateien gescannt
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ collect($matches)->filter(fn($m) => !empty($m['matches']))->count() }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Mit Übereinstimmungen
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ collect($matches)->pluck('matches')->flatten(1)->count() }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Gesamt Matches
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

