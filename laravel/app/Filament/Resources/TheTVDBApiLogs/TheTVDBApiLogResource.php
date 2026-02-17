<?php

namespace App\Filament\Resources\TheTVDBApiLogs;

use App\Filament\Resources\TheTVDBApiLogs\Pages\ListTheTVDBApiLogs;
use App\Filament\Resources\TheTVDBApiLogs\Pages\TheTVDBApiStatistics;
use App\Filament\Resources\TheTVDBApiLogs\Pages\ViewTheTVDBApiLog;
use App\Filament\Resources\TheTVDBApiLogs\Schemas\TheTVDBApiLogInfolist;
use App\Filament\Resources\TheTVDBApiLogs\Tables\TheTVDBApiLogsTable;
use App\Models\TheTVDBApiLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TheTVDBApiLogResource extends Resource
{
    protected static ?string $model = TheTVDBApiLog::class;
    protected static ?string $slug = 'the-tvdb-api-logs';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;
    protected static string|null|\UnitEnum $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 91;

    protected static ?string $navigationLabel = 'API Logs';

    protected static ?string $modelLabel = 'API Log';

    protected static ?string $pluralModelLabel = 'API Logs';

    public static function infolist(Schema $schema): Schema
    {
        return TheTVDBApiLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TheTVDBApiLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTheTVDBApiLogs::route('/'),
            'statistics' => TheTVDBApiStatistics::route('/statistics'),
            'view' => ViewTheTVDBApiLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}

