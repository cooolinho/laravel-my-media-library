<?php

namespace App\Services;

use App\Contracts\TheTVDBSchema\ArtworkBaseRecord;
use App\Http\Client\TheTVDB\Api\EpisodesApi;
use App\Http\Client\TheTVDB\Api\LanguagesApi;
use App\Http\Client\TheTVDB\Api\SeriesApi;
use App\Models\Artwork;
use App\Models\Episode;
use App\Models\Language;
use App\Models\Series;
use App\Models\TheTvDB\EpisodeData;
use App\Models\TheTvDB\EpisodeTranslation;
use App\Models\TheTvDB\SeriesData;
use App\Models\TheTvDB\SeriesTranslation;

class ImportDataService
{
    /**
     * @param SeriesApi $seriesApi
     * @param EpisodesApi $episodesApi
     * @param LanguagesApi $languagesApi
     */
    public function __construct(
        protected SeriesApi   $seriesApi,
        protected EpisodesApi $episodesApi,
        protected LanguagesApi $languagesApi,
    )
    {

    }

    /**
     * @param Series $series
     * @return SeriesData
     */
    public function importSeriesData(Series $series): SeriesData
    {
        $data = $this->seriesApi->getSeriesBase($series->theTvDbId)->getData();
        $translations = $this->seriesApi->getSeriesTranslations($series->theTvDbId);

        $rawData = array_merge($data, [
            SeriesData::status => $data[SeriesData::status]['name'] ?? null,
        ]);

        /** @var SeriesData $seriesData */
        $seriesData = SeriesData::query()->updateOrCreate([
            SeriesData::series_id => $series->id,
        ], $this->filterResponseData($rawData));

        // translations
        $seriesData->translations()->delete();
        foreach ($translations as $lang => $translation) {
            $seriesData->translations()->create([
                SeriesTranslation::lang => $lang ?? null,
                SeriesTranslation::name => $translation['name'] ?? null,
                SeriesTranslation::overview => $translation['overview'] ?? null,
            ]);
        }

        return $seriesData;
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

    /**
     * @param Episode $episode
     * @return EpisodeData
     */
    public function importEpisodesData(Episode $episode): EpisodeData
    {
        $data = $this->episodesApi->getEpisodeBase($episode->theTvDbId)->getData();
        $translations = $this->episodesApi->getEpisodeTranslations($episode->theTvDbId);

        $episodeData = EpisodeData::query()->updateOrCreate([
            EpisodeData::belongs_to_episode => $episode->id,
        ], $data);

        // translations
        $episodeData->translations()->delete();
        foreach ($translations as $lang => $translation) {
            $episodeData->translations()->create([
                EpisodeTranslation::lang => $lang ?? null,
                EpisodeTranslation::name => $translation['name'] ?? null,
                EpisodeTranslation::overview => $translation['overview'] ?? null,
            ]);
        }

        return $episodeData;
    }

    /**
     * @return void
     */
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
     * @param Series $series
     * @return void
     */
    public function importSeriesArtworks(Series $series): void
    {
        $data = $this->seriesApi->getSeriesArtworks($series->theTvDbId)->getData()['artworks'] ?? [];
        $artworks = [];
        foreach ($data as $artwork) {
            $artworks[] = [
                Artwork::series_id => $series->id,
                Artwork::theTvDbId => $artwork[ArtworkBaseRecord::id],
                Artwork::image => $artwork[ArtworkBaseRecord::image],
                Artwork::thumbnail => $artwork[ArtworkBaseRecord::thumbnail],
                Artwork::type => $artwork[ArtworkBaseRecord::type],
            ];
        }

        if (empty($artworks)) {
            return;
        }

        Artwork::query()->upsert($artworks, Artwork::theTvDbId);
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
