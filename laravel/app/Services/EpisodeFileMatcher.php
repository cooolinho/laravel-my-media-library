<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Series;
use App\Models\TheTvDB\EpisodeTranslation;
use App\Settings\TheTVDBSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Service zum Abgleich von Dateinamen mit Episodentiteln
 */
class EpisodeFileMatcher
{
    /**
     * Matcht eine Liste von Dateinamen mit den Episoden einer Serie
     *
     * @param Series $series Die Serie, deren Episoden gematcht werden sollen
     * @param array $fileNames Array von Dateinamen
     * @return Collection Collection mit Match-Ergebnissen
     */
    public function matchFiles(Series $series, array $fileNames): Collection
    {
        $theTVDBSettings = new TheTVDBSettings();
        $episodes = $series->episodes()
            ->with(['data.translations'])
            ->orderBy(Episode::seasonNumber)
            ->orderBy(Episode::number)
            ->get();

        $results = collect();

        foreach ($fileNames as $fileName) {
            $cleanFileName = $this->cleanFileName($fileName);

            $matches = $episodes->map(function (Episode $episode) use ($cleanFileName, $fileName, $theTVDBSettings) {
                $episodeTitle = $this->getEpisodeTitle($episode, $theTVDBSettings->languageDefault);

                if (!$episodeTitle) {
                    return null;
                }

                $cleanEpisodeTitle = $this->cleanTitle($episodeTitle);
                $similarity = $this->calculateSimilarity($cleanFileName, $cleanEpisodeTitle);

                // Nur Matches über 40% Ähnlichkeit berücksichtigen
                if ($similarity < 40) {
                    return null;
                }

                return [
                    'episode' => $episode,
                    'episode_title' => $episodeTitle,
                    'similarity' => $similarity,
                ];
            })
                ->filter()
                ->sortByDesc('similarity')
                ->take(3); // Top 3 Matches pro Datei

            $results->push([
                'file_name' => $fileName,
                'matches' => $matches->values(),
            ]);
        }

        return $results;
    }

    /**
     * Bereinigt einen Dateinamen für den Vergleich
     * Extrahiert nur den Titel nach dem Episoden-Pattern
     */
    protected function cleanFileName(string $fileName): string
    {
        // Dateiendung entfernen
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);

        // Extrahiere nur den Teil nach dem Episoden-Pattern
        // Patterns: S01E001, S01E01, 1x01, etc., optional gefolgt von " - " oder " "
        if (preg_match('/[Ss]\d{1,2}[Ee]\d{1,4}\s*-?\s*(.+)$/i', $fileName, $matches)) {
            // Nimm den Teil nach dem Episoden-Pattern
            $fileName = $matches[1];
        } elseif (preg_match('/\d{1,2}x\d{1,4}\s*-?\s*(.+)$/i', $fileName, $matches)) {
            // Alternative Pattern wie 1x01
            $fileName = $matches[1];
        } else {
            // Falls kein Pattern gefunden wurde, entferne es manuell
            $fileName = preg_replace('/^[Ss]\d{1,2}[Ee]\d{1,4}\s*-?\s*/i', '', $fileName);
            $fileName = preg_replace('/^\d{1,2}x\d{1,4}\s*-?\s*/i', '', $fileName);
        }

        // Auflösungen entfernen (720p, 1080p, etc.)
        $fileName = preg_replace('/\d{3,4}[pi]\b/', '', $fileName);

        // Gängige Video-Tags entfernen
        $patterns = [
            '/\b(HDTV|WEB-?DL|WEBRip|BluRay|BDRip|DVDRip|x264|x265|h264|h265|HEVC|AAC|AC3|DD5\.1|DTS)\b/i',
            '/\b(PROPER|REPACK|INTERNAL|LIMITED)\b/i',
            '/\[(.*?)\]/', // Alles in eckigen Klammern
            '/\((.*?)\)/', // Alles in runden Klammern
        ];

        foreach ($patterns as $pattern) {
            $fileName = preg_replace($pattern, ' ', $fileName);
        }

        return $this->normalizeString($fileName);
    }

    /**
     * Normalisiert einen String für den Vergleich
     */
    protected function normalizeString(string $string): string
    {
        // Zu Kleinbuchstaben
        $string = Str::lower($string);

        // Sonderzeichen durch Leerzeichen ersetzen
        $string = preg_replace('/[^a-z0-9\s]/', ' ', $string);

        // Mehrfache Leerzeichen durch einzelnes ersetzen
        $string = preg_replace('/\s+/', ' ', $string);

        // Trimmen
        return trim($string);
    }

    /**
     * Holt den Titel einer Episode (bevorzugt Deutsch, sonst Englisch)
     */
    protected function getEpisodeTitle(Episode $episode, $preferredLang): ?string
    {
        if (!$episode->data) {
            return null;
        }

        // Bevorzuge deutsche Übersetzung
        $translation = $episode->data->translations()
            ->firstWhere('lang', $preferredLang);

        /** @var EpisodeTranslation|null $translation */
        if ($translation && $translation->name) {
            return $translation->name;
        }

        // Fallback auf englische Übersetzung
        $translation = collect($episode->data->translations)
            ->firstWhere('lang', 'eng');

        return $translation?->name;
    }

    /**
     * Bereinigt einen Episodentitel für den Vergleich
     */
    protected function cleanTitle(string $title): string
    {
        return $this->normalizeString($title);
    }

    /**
     * Berechnet die Ähnlichkeit zwischen zwei Strings
     */
    protected function calculateSimilarity(string $str1, string $str2): float
    {
        // Levenshtein-Distanz für kurze Strings
        if (strlen($str1) < 255 && strlen($str2) < 255) {
            $maxLen = max(strlen($str1), strlen($str2));
            if ($maxLen === 0) {
                return 100;
            }
            $lev = levenshtein($str1, $str2);
            $similarity = (1 - ($lev / $maxLen)) * 100;
        } else {
            // similar_text für längere Strings
            similar_text($str1, $str2, $similarity);
        }

        // Bonus für exakte Wortübereinstimmungen
        $words1 = explode(' ', $str1);
        $words2 = explode(' ', $str2);
        $commonWords = count(array_intersect($words1, $words2));
        $totalWords = max(count($words1), count($words2));

        if ($totalWords > 0) {
            $wordBonus = ($commonWords / $totalWords) * 20;
            $similarity = min(100, $similarity + $wordBonus);
        }

        return round($similarity, 2);
    }

    /**
     * Exportiert die Matches als Array für die Anzeige
     */
    public function formatMatches(Collection $matches): array
    {
        return $matches->map(function ($match) {
            return [
                'file_name' => $match['file_name'],
                'matches' => collect($match['matches'])->map(function ($episodeMatch) {
                    /** @var Episode $episode */
                    $episode = $episodeMatch['episode'];

                    return [
                        'episode_id' => $episode->id,
                        'identifier' => $episode->getIdentifier(),
                        'title' => $episodeMatch['episode_title'],
                        'similarity' => $episodeMatch['similarity'],
                        'season' => $episode->seasonNumber,
                        'episode' => $episode->number,
                        'owned' => $episode->owned,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }
}

