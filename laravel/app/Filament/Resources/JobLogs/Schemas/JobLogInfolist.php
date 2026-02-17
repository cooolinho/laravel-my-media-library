<?php

namespace App\Filament\Resources\JobLogs\Schemas;

use App\Filament\Resources\Episodes\EpisodeResource;
use App\Filament\Resources\Series\SeriesResource;
use App\Models\Episode;
use App\Models\JobLog;
use App\Models\Series;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class JobLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Job-Informationen')
                    ->schema([
                        TextEntry::make('job_name')
                            ->label('Job Name')
                            ->size(TextSize::Large)
                            ->weight('bold'),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn(JobLog $record): string => $record->getStatusColor())
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                JobLog::STATUS_STARTED => 'Gestartet',
                                JobLog::STATUS_SUCCESS => 'Erfolgreich',
                                JobLog::STATUS_FAILED => 'Fehlgeschlagen',
                                JobLog::STATUS_SKIPPED => 'Übersprungen',
                                default => $state,
                            })
                            ->size(TextSize::Large),

                        TextEntry::make('message')
                            ->label('Nachricht')
                            ->columnSpanFull(),

                        TextEntry::make('created_at')
                            ->label('Gestartet am')
                            ->dateTime('d.m.Y H:i:s'),

                        TextEntry::make('finished_at')
                            ->label('Beendet am')
                            ->dateTime('d.m.Y H:i:s')
                            ->placeholder('-'),

                        TextEntry::make('duration_seconds')
                            ->label('Dauer')
                            ->formatStateUsing(fn(?float $state): string => $state ? round($state, 3) . ' Sekunden' : '-'),
                    ])
                    ->columns(3),

                Section::make('Verknüpftes Objekt')
                    ->schema([
                        TextEntry::make('loggable_type')
                            ->label('Typ')
                            ->formatStateUsing(function (?string $state): string {
                                if (!$state) {
                                    return 'Kein Objekt verknüpft';
                                }
                                $parts = explode('\\', $state);
                                return end($parts);
                            }),

                        TextEntry::make('loggable')
                            ->label('Name')
                            ->formatStateUsing(function (JobLog $record): string {
                                if (!$record->loggable) {
                                    return '-';
                                }

                                if ($record->loggable instanceof Series) {
                                    return $record->loggable->name;
                                }

                                if ($record->loggable instanceof Episode) {
                                    return $record->loggable->series->name . ' - ' . $record->loggable->getIdentifier();
                                }

                                return '-';
                            })
                            ->url(function (JobLog $record): ?string {
                                if (!$record->loggable) {
                                    return null;
                                }

                                if ($record->loggable instanceof Series) {
                                    return SeriesResource::getUrl('view', ['record' => $record->loggable]);
                                }

                                if ($record->loggable instanceof Episode) {
                                    return EpisodeResource::getUrl('view', ['record' => $record->loggable]);
                                }

                                return null;
                            }, shouldOpenInNewTab: true),
                    ])
                    ->columns(2)
                    ->visible(fn(JobLog $record) => $record->loggable !== null),

                Section::make('Kontext')
                    ->schema([
                        ViewEntry::make('context')
                            ->label('')
                            ->view('filament.infolists.entries.json-display')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn(JobLog $record) => !empty($record->context))
                    ->collapsible(),

                Section::make('Fehlerdetails')
                    ->schema([
                        TextEntry::make('exception')
                            ->label('')
                            ->formatStateUsing(fn(?string $state): string => $state ?? '-')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn(JobLog $record) => $record->isFailed() && $record->exception)
                    ->collapsible(),
            ]);
    }
}
