<?php

namespace App\Services;

use App\Http\Client\TheTVDB\TheTVDBApi;
use App\Models\Episode;
use App\Models\Series;
use App\Models\TheTvDB\EpisodeData;
use App\Models\TheTvDB\SeriesData;

class ImportDataService
{
    public function __construct(protected TheTVDBApi $api)
    {

    }

    public function importSeriesData(Series $series): SeriesData
    {
        $data = $this->api->getSeries($series->theTvDbId)->getData();
        $translations = $this->api->getSeriesTranslations($series);

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
        $data = $this->api->getSeriesEpisodes($series->theTvDbId)->getData()['episodes'] ?? [];

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
        $data = $this->api->getEpisode($episode->theTvDbId)->getData();
        $translations = $this->api->getEpisodeTranslations($episode->theTvDbId);

        return EpisodeData::query()->updateOrCreate([
            EpisodeData::belongs_to_episode => $episode->id,
        ], array_merge($data, [
            EpisodeData::translations => $translations,
        ]));
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
