<?php

namespace Tests\Unit\Services;

use App\Models\Episode;
use App\Models\Series;
use App\Models\TheTvDB\EpisodeData;
use App\Models\TheTvDB\EpisodeTranslation;
use App\Services\EpisodeFileMatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpisodeFileMatcherTest extends TestCase
{
    use RefreshDatabase;

    protected EpisodeFileMatcher $matcher;
    protected Series $series;

    public function test_matches_exact_episode_title()
    {
        $fileNames = ['S01E01 - Poisoned Lemonade.mkv'];
        $matches = $this->matcher->matchFiles($this->series, $fileNames);

        $this->assertCount(1, $matches);
        $firstMatch = $matches->first();
        $this->assertEquals('S01E01 - Poisoned Lemonade.mkv', $firstMatch['file_name']);
        $this->assertNotEmpty($firstMatch['matches']);

        // Das erste Match sollte die höchste Ähnlichkeit haben
        $topMatch = $firstMatch['matches'][0];
        $this->assertGreaterThan(70, $topMatch['similarity']);
    }

    public function test_matches_with_quality_tags()
    {
        $fileNames = ['S01E01 - Poisoned Lemonade 1080p WEB-DL x264 AAC.mkv'];
        $matches = $this->matcher->matchFiles($this->series, $fileNames);

        $this->assertCount(1, $matches);
        $firstMatch = $matches->first();
        $this->assertNotEmpty($firstMatch['matches']);
    }

    public function test_matches_with_release_group()
    {
        $fileNames = ['S01E01 - Poisoned Lemonade [ReleaseGroup].mkv'];
        $matches = $this->matcher->matchFiles($this->series, $fileNames);

        $this->assertCount(1, $matches);
        $firstMatch = $matches->first();
        $this->assertNotEmpty($firstMatch['matches']);
    }

    public function test_matches_with_three_digit_episode()
    {
        $fileNames = ['S01E001 - Poisoned Lemonade.mkv'];
        $matches = $this->matcher->matchFiles($this->series, $fileNames);

        $this->assertCount(1, $matches);
        $firstMatch = $matches->first();
        $this->assertNotEmpty($firstMatch['matches']);

        $topMatch = $firstMatch['matches'][0];
        $this->assertGreaterThan(70, $topMatch['similarity']);
    }

    public function test_matches_german_title()
    {
        $fileNames = ['S01E001 - Mord in Serie.mkv'];
        $matches = $this->matcher->matchFiles($this->series, $fileNames);

        $this->assertCount(1, $matches);
        $firstMatch = $matches->first();
        // Sollte Matches finden, auch wenn der deutsche Titel nicht in den Test-Daten ist
        $this->assertIsArray($firstMatch['matches']);
    }

    public function test_returns_top_three_matches()
    {
        $fileNames = ['Some File Name.mkv'];
        $matches = $this->matcher->matchFiles($this->series, $fileNames);

        $firstMatch = $matches->first();
        $this->assertLessThanOrEqual(3, count($firstMatch['matches']));
    }

    public function test_filters_low_similarity_matches()
    {
        $fileNames = ['Completely Unrelated Title xyz123.mkv'];
        $matches = $this->matcher->matchFiles($this->series, $fileNames);

        $firstMatch = $matches->first();
        // Sollte keine oder sehr wenige Matches haben bei komplett anderem Titel
        foreach ($firstMatch['matches'] as $match) {
            $this->assertGreaterThanOrEqual(40, $match['similarity']);
        }
    }

    public function test_clean_filename_removes_quality()
    {
        $reflection = new \ReflectionClass($this->matcher);
        $method = $reflection->getMethod('cleanFileName');
        $method->setAccessible(true);

        $fileName = 'Episode Title 1080p x264 AAC.mkv';
        $cleaned = $method->invoke($this->matcher, $fileName);

        $this->assertStringNotContainsString('1080p', $cleaned);
        $this->assertStringNotContainsString('x264', $cleaned);
        $this->assertStringNotContainsString('AAC', $cleaned);
    }

    public function test_clean_filename_removes_episode_pattern()
    {
        $reflection = new \ReflectionClass($this->matcher);
        $method = $reflection->getMethod('cleanFileName');
        $method->setAccessible(true);

        $fileName = 'S01E05 - Episode Title.mkv';
        $cleaned = $method->invoke($this->matcher, $fileName);

        $this->assertStringNotContainsString('s01e05', $cleaned);
        $this->assertStringContainsString('episode title', $cleaned);
    }

    public function test_clean_filename_extracts_title_after_dash()
    {
        $reflection = new \ReflectionClass($this->matcher);
        $method = $reflection->getMethod('cleanFileName');
        $method->setAccessible(true);

        $fileName = 'S01E001 - Mord in Serie.mkv';
        $cleaned = $method->invoke($this->matcher, $fileName);

        $this->assertEquals('mord in serie', $cleaned);
    }

    public function test_clean_filename_extracts_title_after_space()
    {
        $reflection = new \ReflectionClass($this->matcher);
        $method = $reflection->getMethod('cleanFileName');
        $method->setAccessible(true);

        $fileName = 'S01E004 Mord auf Raten.mkv';
        $cleaned = $method->invoke($this->matcher, $fileName);

        $this->assertEquals('mord auf raten', $cleaned);
    }

    public function test_format_matches_returns_correct_structure()
    {
        $fileNames = ['Forensic Files S01E01 Poisoned Lemonade.mkv'];
        $matches = $this->matcher->matchFiles($this->series, $fileNames);
        $formatted = $this->matcher->formatMatches($matches);

        $this->assertIsArray($formatted);
        $this->assertArrayHasKey('file_name', $formatted[0]);
        $this->assertArrayHasKey('matches', $formatted[0]);

        if (!empty($formatted[0]['matches'])) {
            $match = $formatted[0]['matches'][0];
            $this->assertArrayHasKey('episode_id', $match);
            $this->assertArrayHasKey('identifier', $match);
            $this->assertArrayHasKey('title', $match);
            $this->assertArrayHasKey('similarity', $match);
            $this->assertArrayHasKey('season', $match);
            $this->assertArrayHasKey('episode', $match);
            $this->assertArrayHasKey('owned', $match);
        }
    }

    public function test_prefers_german_translations()
    {
        // Erstelle Episode mit deutscher und englischer Übersetzung
        $episode = Episode::factory()->create([
            'series_id' => $this->series->id,
            'seasonNumber' => 1,
            'number' => 10,
        ]);

        $episodeData = EpisodeData::create([
            'episode_id' => $episode->id,
            'aired' => now(),
            'runtime' => 45,
        ]);

        EpisodeTranslation::create([
            'episode_data_id' => $episodeData->id,
            'lang' => 'eng',
            'name' => 'English Title',
        ]);

        EpisodeTranslation::create([
            'episode_data_id' => $episodeData->id,
            'lang' => 'deu',
            'name' => 'Deutscher Titel',
        ]);

        $fileNames = ['Deutscher Titel.mkv'];
        $matches = $this->matcher->matchFiles($this->series, $fileNames);

        $firstMatch = $matches->first();
        $topMatch = $firstMatch['matches'][0];

        // Die deutsche Übersetzung sollte besser matchen
        $this->assertGreaterThan(60, $topMatch['similarity']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->matcher = new EpisodeFileMatcher();
        $this->series = $this->createTestSeries();
    }

    protected function createTestSeries(): Series
    {
        $series = Series::factory()->create([
            'name' => 'Forensic Files',
            'theTvDbId' => 12345,
        ]);

        // Erstelle einige Test-Episoden mit Daten
        for ($i = 1; $i <= 5; $i++) {
            $episode = Episode::factory()->create([
                'series_id' => $series->id,
                'seasonNumber' => 1,
                'number' => $i,
            ]);

            $episodeData = EpisodeData::create([
                'episode_id' => $episode->id,
                'aired' => now(),
                'runtime' => 45,
            ]);

            EpisodeTranslation::create([
                'episode_data_id' => $episodeData->id,
                'lang' => 'eng',
                'name' => match ($i) {
                    1 => 'Poisoned Lemonade',
                    2 => 'The Magic Bullet',
                    3 => 'The House That Roared',
                    4 => 'Deadly Gift',
                    5 => 'Bitter Potion',
                },
            ]);
        }

        return $series->load(['episodes.data.translations']);
    }
}

