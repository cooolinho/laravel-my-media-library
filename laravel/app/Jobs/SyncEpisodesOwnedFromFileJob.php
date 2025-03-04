<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Models\Series;
use App\Settings\JobSettings;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SyncEpisodesOwnedFromFileJob implements ShouldQueue
{
    use Queueable;

    public const REGEX_SEASON_EPISODE = '/S([0-9]{2})E([0-9]{2,3})/';

    /**
     * key = theTvDbId
     * value = S01E01
     *
     * @var array
     */
    private array $identifier = [];

    /**
     * theTvDbId's
     *
     * @var array
     */
    private array $episodesFound = [];

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Series $series,
        private readonly string $filePath
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(JobSettings $settings): void
    {
        if (!$settings->syncEpisodesOwnedFromFileJob_enabled) {
            $this->fail(new JobNotActivatedException());
            return;
        }

        $this->identifier = $this->series->getEpisodesIdentifier();
        $this->processFile();
    }

    public function processFile(): void
    {
        if (!File::exists($this->filePath)) {
            $this->fail('File not found');
        }

        $lines = [];
        try {
            $lines = File::lines($this->filePath);
        } catch (FileNotFoundException $e) {
            $this->fail($e->getMessage());
        }

        foreach ($lines as $line) {
            $this->processLine($line);
        }

        Log::info('Totally found: '.count($this->episodesFound));
        File::delete($this->filePath);
    }

    public function processLine($line): void
    {
        preg_match_all(self::REGEX_SEASON_EPISODE, $line, $result);
        [$identifier] = $result;

        if (!empty($identifier)) {
            Log::info('Identifier found: '.$this->identifier[$identifier[0]]);
            $this->episodesFound[] = $this->identifier[$identifier[0]];
        }

        Episode::query()
            ->whereIn(Episode::theTvDbId, $this->episodesFound)
            ->update([
                Episode::owned => true,
            ]);
    }
}
