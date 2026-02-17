<?php

namespace App\Filament\Resources\JobLogs;

use App\Filament\Resources\JobLogs\Pages\ListJobLogs;
use App\Filament\Resources\JobLogs\Pages\ViewJobLog;
use App\Filament\Resources\JobLogs\Schemas\JobLogInfolist;
use App\Filament\Resources\JobLogs\Tables\JobLogsTable;
use App\Models\JobLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JobLogResource extends Resource
{
    protected static ?string $model = JobLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static string|null|\UnitEnum $navigationGroup = 'Jobs';
    protected static ?int $navigationSort = 51;

    protected static ?string $navigationLabel = 'Logs';

    protected static ?string $modelLabel = 'Log';

    protected static ?string $pluralModelLabel = 'Logs';

    public static function infolist(Schema $schema): Schema
    {
        return JobLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobLogs::route('/'),
            'view' => ViewJobLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
