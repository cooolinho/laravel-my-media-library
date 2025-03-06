<?php

namespace App\Services;

use App\Http\Client\TheTVDB\Api\EpisodesApi;
use App\Http\Client\TheTVDB\Api\LanguagesApi;
use App\Http\Client\TheTVDB\Api\SeriesApi;
use App\Models\Episode;
use App\Models\Language;
use App\Models\Series;
use App\Models\TheTvDB\EpisodeData;
use App\Models\TheTvDB\SeriesData;

class ImportDataService
{
    public function __construct(
        protected SeriesApi   $seriesApi,
        protected EpisodesApi $episodesApi,
        protected LanguagesApi $languagesApi,
    )
    {

    }

    public function importSeriesData(Series $series): SeriesData
    {
        $data = $this->seriesApi->getSeriesBase($series->theTvDbId)->getData();
        $translations = $this->seriesApi->getSeriesTranslations($series->theTvDbId);

        $rawData = array_merge($data, [
            SeriesData::translations => $translations,
            SeriesData::status => $data[SeriesData::status]['name'] ?? null,
        ]);

        return SeriesData::query()->updateOrCreate([
            SeriesData::series_id => $series->id,
        ], $this->filterResponseData($rawData));
    }

    /**
     * @param Series $series
     * @param bool $ignoreSpecials
     * @return array<Episode>
     */
    public function importSeriesEpisodes(Series $series, bool $ignoreSpecials = true): array
    {
        $data = $this->seriesApi->getSeriesEpisodes($series->theTvDbId)->getData()['episodes'] ?? [];

        $episodes = [];
        foreach ($data as $episode) {
            if ($ignoreSpecials && $episode[Episode::seasonNumber] <= 0) {
                continue;
            }

            $episodes[] = Episode::query()->updateOrCreate([
                Episode::series_id => $series->id,
                Episode::theTvDbId => $episode['id'],
            ], $this->filterResponseData($episode));
        }

        return $episodes;
    }

    public function importEpisodesData(Episode $episode): EpisodeData
    {
        $data = $this->episodesApi->getEpisodeBase($episode->theTvDbId)->getData();
        $translations = $this->episodesApi->getEpisodeTranslations($episode->theTvDbId);

        return EpisodeData::query()->updateOrCreate([
            EpisodeData::belongs_to_episode => $episode->id,
        ], array_merge($data, [
            EpisodeData::translations => $translations,
        ]));
    }

    public function importLanguages(): void
    {
        $data = $this->languagesApi->getAllLanguages()->getData();
        $filteredData = array_map(function($entity) {
            // remove shortCode bc is always null
            unset($entity['shortCode']);
            return $entity;
        }, $data);

        Language::query()->upsert($filteredData, Language::id);
    }

    /**
     * removes items with empty or null values
     *
     * @param array $rawData
     * @return array
     */
    protected function filterResponseData(array $rawData): array
    {
        return array_filter($rawData, function ($item) {
            if ($item === "" || $item === null) {
                return false;
            }

            return true;
        });
    }
}
