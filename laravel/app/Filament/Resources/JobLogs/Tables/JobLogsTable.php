<?php

namespace App\Filament\Resources\JobLogs\Tables;

use App\Filament\Resources\Episodes\EpisodeResource;
use App\Filament\Resources\Series\SeriesResource;
use App\Models\Episode;
use App\Models\JobLog;
use App\Models\Series;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('job_name')
                    ->label('Job')
                    ->searchable(query: fn($query, $search) => $query->where('job_class', 'like', "%{$search}%")
                    )
                    ->sortable(query: fn($query, $direction) => $query->orderBy('job_class', $direction)
                    ),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(JobLog $record): string => $record->getStatusColor())
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        JobLog::STATUS_STARTED => 'Gestartet',
                        JobLog::STATUS_SUCCESS => 'Erfolgreich',
                        JobLog::STATUS_FAILED => 'Fehlgeschlagen',
                        JobLog::STATUS_SKIPPED => 'Übersprungen',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('message')
                    ->label('Nachricht')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('loggable_type')
                    ->label('Typ')
                    ->formatStateUsing(function (?string $state): string {
                        if (!$state) {
                            return '-';
                        }
                        $parts = explode('\\', $state);
                        return end($parts);
                    })
                    ->sortable(),

                TextColumn::make('loggable.name')
                    ->label('Bezug')
                    ->getStateUsing(function (JobLog $record): ?string {
                        if (!$record->loggable) {
                            return null;
                        }

                        if ($record->loggable instanceof Series) {
                            return $record->loggable->name;
                        }

                        if ($record->loggable instanceof Episode) {
                            return $record->loggable->series->name . ' - ' . $record->loggable->getIdentifier();
                        }

                        return null;
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
                    }, shouldOpenInNewTab: true)
                    ->searchable(query: function ($query, $search) {
                        $query->where(function (Builder $q) use ($search) {
                            $q->whereHasMorph('loggable', [Series::class], function (Builder $q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                        });
                    }),

                TextColumn::make('duration_seconds')
                    ->label('Dauer')
                    ->formatStateUsing(fn(?float $state): string => $state ? round($state, 2) . 's' : '-'
                    )
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Gestartet am')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                TextColumn::make('finished_at')
                    ->label('Beendet am')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        JobLog::STATUS_STARTED => 'Gestartet',
                        JobLog::STATUS_SUCCESS => 'Erfolgreich',
                        JobLog::STATUS_FAILED => 'Fehlgeschlagen',
                        JobLog::STATUS_SKIPPED => 'Übersprungen',
                    ])
                    ->multiple(),

                SelectFilter::make('job_class')
                    ->label('Job Typ')
                    ->options(function () {
                        return JobLog::query()
                            ->select('job_class')
                            ->distinct()
                            ->pluck('job_class')
                            ->mapWithKeys(function ($jobClass) {
                                $parts = explode('\\', $jobClass);
                                $name = end($parts);
                                return [$jobClass => $name];
                            })
                            ->toArray();
                    })
                    ->multiple(),

                SelectFilter::make('loggable_type')
                    ->label('Bezugstyp')
                    ->options([
                        Series::class => 'Serie',
                        Episode::class => 'Episode',
                    ])
                    ->multiple(),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh alle 30 Sekunden
    }
}

