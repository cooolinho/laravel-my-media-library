<?php

namespace App\Filament\Widgets;

use App\Models\Job;
use App\Settings\DashboardSettings;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Str;

class RecentJobsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return app(DashboardSettings::class)->show_recent_jobs;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Job::query()
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->heading('Letzte Jobs in der Warteschlange')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('queue')
                    ->label('Queue')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('displayName')
                    ->label('Job Name')
                    ->getStateUsing(function (Job $record): string {
                        $payload = $record->payload;
                        $displayName = $payload['displayName'] ?? 'Unbekannt';
                        return Str::limit($displayName, 50);
                    })
                    ->searchable()
                    ->tooltip(function (Job $record): string {
                        $payload = $record->payload;
                        return $payload['displayName'] ?? 'Unbekannt';
                    }),

                TextColumn::make('attempts')
                    ->label('Versuche')
                    ->badge()
                    ->color(fn(int $state): string => $state > 0 ? 'warning' : 'success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Erstellt')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->since()
                    ->description(fn(Job $record): string => $record->created_at->format('d.m.Y H:i:s')
                    ),

                TextColumn::make('available_at')
                    ->label('Verfügbar ab')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25]);
    }
}

