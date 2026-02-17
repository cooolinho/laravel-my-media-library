<?php

namespace App\Filament\Resources\TheTVDBApiLogs\Schemas;

use App\Models\TheTVDBApiLog;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;

class TheTVDBApiLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Request Information')
                    ->columnSpanFull()
                    ->schema([
                        Group::make([
                            TextEntry::make(TheTVDBApiLog::id)
                                ->label('ID'),

                            TextEntry::make(TheTVDBApiLog::endpoint)
                                ->label('Endpoint')
                                ->fontFamily(FontFamily::Mono)
                                ->copyable(),

                            TextEntry::make(TheTVDBApiLog::method)
                                ->label('Method')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'GET' => 'success',
                                    'POST' => 'info',
                                    'PUT', 'PATCH' => 'warning',
                                    'DELETE' => 'danger',
                                    default => 'gray',
                                }),
                        ])->columns(3),

                        Group::make([
                            TextEntry::make(TheTVDBApiLog::status_code)
                                ->label('Status Code')
                                ->badge()
                                ->color(fn(?int $state): string => match (true) {
                                    $state === null => 'gray',
                                    $state >= 200 && $state < 300 => 'success',
                                    $state >= 300 && $state < 400 => 'info',
                                    $state >= 400 && $state < 500 => 'warning',
                                    $state >= 500 => 'danger',
                                    default => 'gray',
                                }),

                            TextEntry::make(TheTVDBApiLog::response_time)
                                ->label('Response Time')
                                ->formatStateUsing(fn(?int $state): string => $state ? $state . ' ms' : '-')
                                ->badge()
                                ->color(fn(?int $state): string => match (true) {
                                    $state === null => 'gray',
                                    $state < 100 => 'success',
                                    $state < 500 => 'warning',
                                    $state >= 500 => 'danger',
                                    default => 'gray',
                                }),

                            TextEntry::make(TheTVDBApiLog::success)
                                ->label('Success')
                                ->badge()
                                ->formatStateUsing(fn(bool $state): string => $state ? 'Success' : 'Failed')
                                ->color(fn(bool $state): string => $state ? 'success' : 'danger'),

                            TextEntry::make(TheTVDBApiLog::from_cache)
                                ->label('From Cache')
                                ->badge()
                                ->formatStateUsing(fn(bool $state): string => $state ? 'Yes' : 'No')
                                ->color(fn(bool $state): string => $state ? 'info' : 'gray'),
                        ])->columns(4),

                        TextEntry::make(TheTVDBApiLog::created_at)
                            ->label('Created At')
                            ->dateTime('d.m.Y H:i:s'),
                    ]),

                Section::make('Parameters')
                    ->columnSpanFull()
                    ->schema([
                        ViewEntry::make(TheTVDBApiLog::params)
                            ->label('')
                            ->view('filament.infolists.entries.json-display')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Response Data')
                    ->columnSpanFull()
                    ->schema([
                        ViewEntry::make(TheTVDBApiLog::response_data)
                            ->label('')
                            ->view('filament.infolists.entries.json-display')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Error')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make(TheTVDBApiLog::error_message)
                            ->label('')
                            ->formatStateUsing(fn(?string $state): string => $state ?? 'No error')
                            ->fontFamily(FontFamily::Mono)
                            ->color(fn(?string $state): string => $state ? 'danger' : 'success')
                            ->copyable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn($record) => !$record->error_message),

                Section::make('Authentication')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make(TheTVDBApiLog::bearer_token_hash)
                            ->label('Bearer Token Hash')
                            ->fontFamily(FontFamily::Mono)
                            ->copyable()
                            ->formatStateUsing(fn(?string $state): string => $state ?? 'N/A'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}

