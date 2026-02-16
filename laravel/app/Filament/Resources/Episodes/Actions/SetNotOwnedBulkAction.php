<?php

namespace App\Filament\Resources\Episodes\Actions;

use App\Models\Episode;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class SetNotOwnedBulkAction
{
    public static function make(?string $name = null): BulkAction
    {
        return BulkAction::make($name ?? 'setNotOwned')
            ->label('Als nicht vorhanden markieren')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Episoden als nicht vorhanden markieren?')
            ->modalDescription('Möchten Sie alle ausgewählten Episoden als nicht vorhanden markieren?')
            ->modalSubmitActionLabel('Als nicht vorhanden markieren')
            ->action(function (Collection $records) {
                $count = $records->count();

                $records->each(function (Episode $record) {
                    $record->owned = false;
                    $record->save();
                });

                Notification::make()
                    ->title('Episoden aktualisiert')
                    ->body("{$count} " . ($count === 1 ? 'Episode wurde' : 'Episoden wurden') . ' als nicht vorhanden markiert.')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}


