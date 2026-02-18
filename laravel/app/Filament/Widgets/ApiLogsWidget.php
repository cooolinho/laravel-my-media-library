<?php

namespace App\Filament\Widgets;

use App\Models\TheTVDBApiLog;
use App\Settings\DashboardSettings;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ApiLogsWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return app(DashboardSettings::class)->show_api_logs;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TheTVDBApiLog::query()
                    ->orderBy('created_at', 'desc')
                    ->limit(15)
            )
            ->heading('Letzte API Aufrufe (TheTVDB)')
            ->columns([
                IconColumn::make('success')
                    ->label('Status')
                    ->boolean()
                    ->listWithLineBreaks()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('status_code')
                    ->label('Code')
                    ->badge()
                    ->color(fn(?int $state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 400 && $state < 500 => 'warning',
                        $state >= 500 => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('method')
                    ->label('Methode')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'GET' => 'success',
                        'POST' => 'info',
                        'PUT' => 'warning',
                        'DELETE' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('endpoint')
                    ->label('Endpoint')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn(TheTVDBApiLog $record): string => $record->endpoint),

                IconColumn::make('from_cache')
                    ->label('Cache')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedBold)
                    ->falseIcon(Heroicon::OutlinedCloud)
                    ->trueColor('warning')
                    ->falseColor('info')
                    ->sortable()
                    ->tooltip(fn(TheTVDBApiLog $record): string => $record->from_cache ? 'Aus Cache' : 'Live Aufruf'
                    ),

                TextColumn::make('response_time')
                    ->label('Zeit (ms)')
                    ->numeric(decimalPlaces: 0)
                    ->sortable()
                    ->color(fn(?float $state): string => match (true) {
                        $state === null => 'gray',
                        $state < 500 => 'success',
                        $state < 1000 => 'warning',
                        default => 'danger',
                    }),

                TextColumn::make('created_at')
                    ->label('Zeitpunkt')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->since()
                    ->description(fn(TheTVDBApiLog $record): string => $record->created_at->format('d.m.Y H:i:s')
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('success')
                    ->label('Erfolg')
                    ->placeholder('Alle')
                    ->trueLabel('Erfolgreich')
                    ->falseLabel('Fehlgeschlagen'),
                Tables\Filters\TernaryFilter::make('from_cache')
                    ->label('Quelle')
                    ->placeholder('Alle')
                    ->trueLabel('Cache')
                    ->falseLabel('Live'),
            ])
            ->paginated([5, 10, 25]);
    }
}

