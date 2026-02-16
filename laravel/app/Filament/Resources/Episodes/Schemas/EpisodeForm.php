<?php

namespace App\Filament\Resources\Episodes\Schemas;

use App\Models\Episode;
use App\Models\Series;
use App\Models\TheTvDB\EpisodeData;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EpisodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getComponents());
    }

    /**
     * @param bool $showSeriesField
     * @return array
     */
    public static function getComponents(bool $showSeriesField = true): array
    {
        return [
            // Basis-Informationen Section
            Section::make('Basis-Informationen')
                ->description('Grundlegende Episode-Daten')
                ->icon(Heroicon::OutlinedInformationCircle)
                ->columnSpanFull()
                ->schema([
                    Select::make(Episode::series_id)
                        ->label('Serie')
                        ->relationship(Episode::belongs_to_series, Series::name)
                        ->required()
                        ->visible($showSeriesField)
                        ->searchable()
                        ->preload()
                        ->columnSpan(2),

                    TextInput::make(Episode::seasonNumber)
                        ->label('Staffel')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(1)
                        ->columnSpan(1),

                    TextInput::make(Episode::number)
                        ->label('Episode')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->columnSpan(1),

                    TextInput::make(Episode::theTvDbId)
                        ->label('TheTVDB ID')
                        ->required()
                        ->numeric()
                        ->columnSpan(2),

                    Toggle::make(Episode::owned)
                        ->label('In Besitz')
                        ->default(false)
                        ->inline(false)
                        ->helperText('Markieren Sie diese Episode als in Ihrem Besitz')
                        ->columnSpan(2),
                ]),

            // Episode-Details Section
            Section::make('Episode-Details')
                ->description('Detaillierte Informationen zur Episode (TheTVDB-Daten)')
                ->icon(Heroicon::OutlinedFilm)
                ->columnSpanFull()
                ->relationship(Episode::has_one_data)
                ->schema([
//                    TextInput::make('name')
//                        ->label('Titel')
//                        ->maxLength(255)
//                        ->columnSpan(2)
//                        ->placeholder('Titel der Episode')
//                        ->helperText('Der offizielle Titel der Episode'),
//
//                    Textarea::make(EpisodeData::overview)
//                        ->label('Beschreibung')
//                        ->rows(4)
//                        ->columnSpan(2)
//                        ->placeholder('Kurze Zusammenfassung der Episode...')
//                        ->helperText('Eine kurze Zusammenfassung des Inhalts'),

                    DatePicker::make(EpisodeData::aired)
                        ->label('Ausstrahlungsdatum')
                        ->displayFormat('d.m.Y')
                        ->native(false)
                        ->columnSpan(1)
                        ->helperText('Datum der Erstausstrahlung'),

                    TextInput::make(EpisodeData::year)
                        ->label('Jahr')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(2100)
                        ->columnSpan(1)
                        ->placeholder('z.B. 2024'),

                    TextInput::make(EpisodeData::runtime)
                        ->label('Laufzeit (Minuten)')
                        ->numeric()
                        ->minValue(1)
                        ->suffix('min')
                        ->columnSpan(1)
                        ->placeholder('z.B. 45'),

                    TextInput::make(EpisodeData::image)
                        ->label('Bild-URL')
                        ->url()
                        ->maxLength(500)
                        ->columnSpan(1)
                        ->placeholder('https://...')
                        ->helperText('URL zum Episode-Thumbnail'),
                ]),

            // Notizen Section
            Section::make('Persönliche Notizen')
                ->columnSpanFull()
                ->description('Ihre eigenen Notizen zur Episode')
                ->icon(Heroicon::OutlinedPencilSquare)
                ->collapsed()
                ->schema([
                    Textarea::make(Episode::notes)
                        ->label('Notizen')
                        ->rows(5)
                        ->placeholder('Ihre persönlichen Notizen zur Episode...')
                        ->helperText('Z.B. Bewertung, Kommentare, Speicherort der Datei, etc.')
                        ->columnSpanFull(),
                ]),
        ];
    }
}


