<?php

namespace App\Filament\Resources\TheTVDBApiLogs\Tables;

use App\Models\TheTVDBApiLog;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TheTVDBApiLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(TheTVDBApiLog::id)
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make(TheTVDBApiLog::endpoint)
                    ->label('Endpoint')
                    ->sortable()
                    ->searchable()
                    ->fontFamily(FontFamily::Mono)
                    ->copyable(),

                TextColumn::make(TheTVDBApiLog::method)
                    ->label('Method')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'GET' => 'success',
                        'POST' => 'info',
                        'PUT', 'PATCH' => 'warning',
                        'DELETE' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make(TheTVDBApiLog::status_code)
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->color(fn(?int $state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 300 && $state < 400 => 'info',
                        $state >= 400 && $state < 500 => 'warning',
                        $state >= 500 => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make(TheTVDBApiLog::response_time)
                    ->label('Response Time')
                    ->sortable()
                    ->formatStateUsing(fn(?int $state): string => $state ? $state . ' ms' : '-')
                    ->color(fn(?int $state): string => match (true) {
                        $state === null => 'gray',
                        $state < 100 => 'success',
                        $state < 500 => 'warning',
                        $state >= 500 => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make(TheTVDBApiLog::success)
                    ->label('Success')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Success' : 'Failed')
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger'),

                TextColumn::make(TheTVDBApiLog::from_cache)
                    ->label('Cached')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Yes' : 'No')
                    ->color(fn(bool $state): string => $state ? 'info' : 'gray'),

                TextColumn::make(TheTVDBApiLog::created_at)
                    ->label('Created At')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make(TheTVDBApiLog::method)
                    ->label('Method')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'PATCH' => 'PATCH',
                        'DELETE' => 'DELETE',
                    ])
                    ->multiple(),

                SelectFilter::make(TheTVDBApiLog::success)
                    ->label('Status')
                    ->options([
                        '1' => 'Success',
                        '0' => 'Failed',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value'])) {
                            return $query;
                        }

                        return $query->where(TheTVDBApiLog::success, (bool)$data['value']);
                    }),

                SelectFilter::make(TheTVDBApiLog::from_cache)
                    ->label('From Cache')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value'])) {
                            return $query;
                        }

                        return $query->where(TheTVDBApiLog::from_cache, (bool)$data['value']);
                    }),

                Filter::make(TheTVDBApiLog::response_time)
                    ->label('Slow Requests (>500ms)')
                    ->query(fn(Builder $query): Builder => $query->where(TheTVDBApiLog::response_time, '>', 500)),

                Filter::make('today')
                    ->label('Today')
                    ->query(fn(Builder $query): Builder => $query->whereDate(TheTVDBApiLog::created_at, today())),

                Filter::make('last_7_days')
                    ->label('Last 7 Days')
                    ->query(fn(Builder $query): Builder => $query->where(TheTVDBApiLog::created_at, '>=', now()->subDays(7))),
            ])
            ->defaultSort(TheTVDBApiLog::created_at, 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}

