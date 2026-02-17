<?php

namespace App\Filament\Resources\Series\Actions;

use App\Http\Client\TheTVDB\Api\Enum\ArtworkTypeEnum;
use App\Models\Artwork;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class DownloadArtworksAsZipAction
{
    public static function make(?string $name = null): Action
    {
        return Action::make($name ?? 'loadArtworksAsZip')
            ->icon(Heroicon::BookmarkSquare)
            ->label('Artworks herunterladen')
            ->color(Color::Teal)
            ->modalHeading('Artworks herunterladen')
            ->modalDescription('Wählen Sie die Artwork-Typen aus, die Sie herunterladen möchten.')
            ->modalSubmitActionLabel('Herunterladen')
            ->modalWidth('lg')
            ->schema([
                CheckboxList::make('artwork_types')
                    ->label('Artwork-Typen')
                    ->options(self::getArtworkTypeOptions())
                    ->default(self::getDefaultSelectedTypes())
                    ->required()
                    ->columns(2)
                    ->helperText('Wählen Sie die Artwork-Typen aus, die heruntergeladen werden sollen.')
            ])
            ->action(function (Series $record, array $data): ?StreamedResponse {
                $selectedTypes = $data['artwork_types'] ?? [];

                if (empty($selectedTypes)) {
                    Notification::make()
                        ->title('Keine Typen ausgewählt')
                        ->body('Bitte wählen Sie mindestens einen Artwork-Typ aus.')
                        ->warning()
                        ->send();
                    return null;
                }

                return self::downloadArtworks($record, $selectedTypes);
            });
    }

    protected static function getArtworkTypeOptions(): array
    {
        return [
            ArtworkTypeEnum::SERIES_POSTER->value => 'Serie - Poster',
            ArtworkTypeEnum::SERIES_BANNER->value => 'Serie - Banner',
            ArtworkTypeEnum::SERIES_BACKGROUND->value => 'Serie - Hintergrund',
            ArtworkTypeEnum::SERIES_CLEARLOGO->value => 'Serie - Clear Logo',
            ArtworkTypeEnum::SERIES_CLEARART->value => 'Serie - Clear Art',
            ArtworkTypeEnum::SERIES_ICON->value => 'Serie - Icon',
            ArtworkTypeEnum::SERIES_CINEMAGRAPH->value => 'Serie - Cinemagraph',
            ArtworkTypeEnum::SEASON_POSTER->value => 'Staffel - Poster',
            ArtworkTypeEnum::SEASON_BANNER->value => 'Staffel - Banner',
            ArtworkTypeEnum::SEASON_BACKGROUND->value => 'Staffel - Hintergrund',
            ArtworkTypeEnum::SEASON_ICON->value => 'Staffel - Icon',
            ArtworkTypeEnum::EPISODE_SCREENCAP_16_9->value => 'Episode - Screenshot (16:9)',
            ArtworkTypeEnum::EPISODE_SCREENCAP_4_3->value => 'Episode - Screenshot (4:3)',
        ];
    }

    protected static function getDefaultSelectedTypes(): array
    {
        // Standardmäßig nur Series-Poster auswählen
        return [ArtworkTypeEnum::SERIES_POSTER->value];
    }

    protected static function downloadArtworks(Series $series, array $selectedTypes): ?StreamedResponse
    {
        $artworks = $series->artworks()
            ->whereIn(Artwork::type, $selectedTypes)
            ->get()
            ->pluck(Artwork::image);

        if ($artworks->isEmpty()) {
            Notification::make()
                ->title('Keine Artworks gefunden')
                ->body('Für die ausgewählten Typen wurden keine Artworks gefunden.')
                ->warning()
                ->send();
            return null;
        }

        $zipFileName = sprintf('artworks-%s.zip', $series->data->slug ?? $series->theTvDbId);
        $zipFilePath = storage_path("app/public/{$zipFileName}");

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($artworks as $imageUrl) {
                $imageContent = self::downloadImage($imageUrl);
                if ($imageContent === false) {
                    continue;
                }

                $imageName = basename(parse_url($imageUrl, PHP_URL_PATH));
                $zip->addFromString($imageName, $imageContent);
            }
            $zip->close();
        }

        return response()->streamDownload(function () use ($zipFilePath) {
            echo file_get_contents($zipFilePath);
            @unlink($zipFilePath);
        }, $zipFileName);
    }

    protected static function downloadImage(string $url): bool|string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode == 200) ? $data : false;
    }
}

