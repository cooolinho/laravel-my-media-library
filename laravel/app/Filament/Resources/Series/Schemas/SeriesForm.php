<?php

namespace App\Filament\Resources\Series\Schemas;

use App\Models\Series;
use App\Models\TheTvDB\SeriesData;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SeriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getComponents());
    }

    public static function getComponents(): array
    {
        return [
            // Basis-Informationen Section
            Section::make('Basis-Informationen')
                ->description('Grundlegende Serien-Daten')
                ->icon(Heroicon::OutlinedInformationCircle)
                ->columnSpanFull()
                ->schema([
                    TextInput::make(Series::name)
                        ->label('Serienname')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2)
                        ->placeholder('Name der Serie')
                        ->helperText('Der offizielle Name der Serie'),

                    TextInput::make(Series::theTvDbId)
                        ->label('TheTVDB ID')
                        ->required()
                        ->numeric()
                        ->columnSpan(2)
                        ->helperText('Die eindeutige ID von TheTVDB'),
                ]),

            // Serien-Details Section
            Section::make('Serien-Details')
                ->description('Detaillierte Informationen zur Serie (TheTVDB-Daten)')
                ->icon(Heroicon::OutlinedFilm)
                ->columnSpanFull()
                ->relationship(Series::has_one_data)
                ->schema([
//                    TextInput::make('name')
//                        ->label('Titel (übersetzt)')
//                        ->maxLength(255)
//                        ->columnSpan(2)
//                        ->placeholder('Übersetzter Titel der Serie')
//                        ->helperText('Der übersetzte Titel der Serie'),
//
//                    Textarea::make(SeriesData::overview)
//                        ->label('Beschreibung')
//                        ->rows(5)
//                        ->columnSpan(2)
//                        ->placeholder('Kurze Zusammenfassung der Serie...')
//                        ->helperText('Eine ausführliche Beschreibung der Serie'),

                    TextInput::make(SeriesData::slug)
                        ->label('Slug')
                        ->maxLength(255)
                        ->columnSpan(2)
                        ->placeholder('serie-slug')
                        ->helperText('URL-freundlicher Identifier'),

                    TextInput::make(SeriesData::image)
                        ->label('Poster-URL')
                        ->url()
                        ->maxLength(500)
                        ->columnSpan(2)
                        ->placeholder('https://...')
                        ->helperText('URL zum Serien-Poster'),
                ]),

            // Ausstrahlungsdaten Section
            Section::make('Ausstrahlungsdaten')
                ->description('Zeitliche Informationen zur Serie')
                ->icon(Heroicon::OutlinedCalendar)
                ->columnSpanFull()
                ->relationship(Series::has_one_data)
                ->schema([
                    DatePicker::make(SeriesData::firstAired)
                        ->label('Erste Ausstrahlung')
                        ->displayFormat('d.m.Y')
                        ->native(false)
                        ->columnSpan(1)
                        ->helperText('Datum der Erstausstrahlung'),

                    DatePicker::make(SeriesData::lastAired)
                        ->label('Letzte Ausstrahlung')
                        ->displayFormat('d.m.Y')
                        ->native(false)
                        ->columnSpan(1)
                        ->helperText('Datum der letzten Ausstrahlung'),

                    DatePicker::make(SeriesData::nextAired)
                        ->label('Nächste Ausstrahlung')
                        ->displayFormat('d.m.Y')
                        ->native(false)
                        ->columnSpan(1)
                        ->helperText('Datum der nächsten Ausstrahlung'),
                ]),

            // Status & Bewertung Section
            Section::make('Status & Bewertung')
                ->description('Status und Bewertungsinformationen')
                ->icon(Heroicon::OutlinedStar)
                ->columnSpanFull()
                ->relationship(Series::has_one_data)
                ->schema([
                    Select::make(SeriesData::status)
                        ->label('Status')
                        ->options([
                            'Continuing' => 'Continuing (Läuft)',
                            'Ended' => 'Ended (Beendet)',
                            'Upcoming' => 'Upcoming (Bevorstehend)',
                            'Pilot' => 'Pilot',
                        ])
                        ->columnSpan(1)
                        ->placeholder('Status auswählen')
                        ->helperText('Aktueller Status der Serie'),

                    TextInput::make(SeriesData::score)
                        ->label('Bewertung')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('/100')
                        ->columnSpan(1)
                        ->placeholder('z.B. 85')
                        ->helperText('Bewertung von 0-100'),

                    TextInput::make(SeriesData::year)
                        ->label('Jahr')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(2100)
                        ->columnSpan(1)
                        ->placeholder('z.B. 2024')
                        ->helperText('Erscheinungsjahr'),

                    TextInput::make(SeriesData::averageRuntime)
                        ->label('Durchschnittliche Laufzeit')
                        ->numeric()
                        ->minValue(1)
                        ->suffix('min')
                        ->columnSpan(1)
                        ->placeholder('z.B. 45')
                        ->helperText('Durchschnittliche Episodenlänge'),
                ]),

            // Herkunft & Sprache Section
            Section::make('Herkunft & Sprache')
                ->description('Ursprungsland und Sprachinformationen')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->columnSpanFull()
                ->relationship(Series::has_one_data)
                ->collapsed()
                ->schema([
                    TextInput::make(SeriesData::originalCountry)
                        ->label('Ursprungsland')
                        ->maxLength(100)
                        ->columnSpan(1)
                        ->placeholder('z.B. USA')
                        ->helperText('Land der Produktion'),

                    TextInput::make(SeriesData::originalLanguage)
                        ->label('Originalsprache')
                        ->maxLength(100)
                        ->columnSpan(1)
                        ->placeholder('z.B. English')
                        ->helperText('Originalsprache der Serie'),
                ]),

            // Erweiterte Einstellungen Section
            Section::make('Erweiterte Einstellungen')
                ->description('Zusätzliche technische Informationen')
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->columnSpanFull()
                ->relationship(Series::has_one_data)
                ->collapsed()
                ->schema([
                    TextInput::make(SeriesData::defaultSeasonType)
                        ->label('Standard-Staffeltyp')
                        ->numeric()
                        ->columnSpan(1)
                        ->placeholder('z.B. 1')
                        ->helperText('TheTVDB Season Type ID'),

                    Toggle::make(SeriesData::isOrderRandomized)
                        ->label('Reihenfolge randomisiert')
                        ->inline(false)
                        ->columnSpan(1)
                        ->helperText('Gibt an, ob die Episodenreihenfolge zufällig ist'),

                    DatePicker::make(SeriesData::lastUpdated)
                        ->label('Zuletzt aktualisiert')
                        ->displayFormat('d.m.Y H:i')
                        ->native(false)
                        ->columnSpan(2)
                        ->helperText('Zeitpunkt der letzten Aktualisierung von TheTVDB'),
                ]),
        ];
    }
}


