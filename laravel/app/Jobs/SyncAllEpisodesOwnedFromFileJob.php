<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Models\Series;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;

class SyncAllEpisodesOwnedFromFileJob implements ShouldQueue
{
    use Queueable;

    public const REGEX_DIRECTORY_SEASON_EPISODE = '/(###SERIES_DIRECTORY###).*?(S([0-9]{2})E([0-9]{2,3}))/';

    /**
     * @var Collection<Series>
     */
    private Collection $series;
    private array $theTvDbIdsFound = [];

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $filePath
    )
    {
        $this->series = new Collection();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->series = Series::all();

        $this->processFile($this->filePath);
    }


    public function processFile($filePath): void
    {
        if (!File::exists($filePath)) {
            $this->fail('File not found');
        }

        $text = File::get($filePath);

        foreach ($this->series as $series) {
            $foundInSeries = $this->findSeriesEpisodes($series, $text);

            $this->theTvDbIdsFound = array_merge(
                $this->theTvDbIdsFound,
                $foundInSeries
            );
        }

        Episode::query()
            ->whereIn(Episode::theTvDbId, $this->theTvDbIdsFound)
            ->update([
                Episode::owned => true,
            ]);

        File::delete($filePath);
    }

    private function findSeriesEpisodes(Series $series, string $text): array
    {
        $seriesEpisodesIdentifier = $series->getEpisodesIdentifier();
        $regex = str_replace('###SERIES_DIRECTORY###', $series->name, self::REGEX_DIRECTORY_SEASON_EPISODE);

        preg_match_all($regex, $text, $result);
        [, , $identifiersFound] = $result;

        return array_values(array_intersect_key($seriesEpisodesIdentifier, array_flip(array_unique($identifiersFound))));
    }
}
