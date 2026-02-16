<?php

namespace App\Filament\Resources\Episodes\Actions;

use App\Models\Episode;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class SetOwnedBulkAction
{
    public static function make(?string $name = null): BulkAction
    {
        return BulkAction::make($name ?? 'setOwned')
            ->label('Als vorhanden markieren')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Episoden als vorhanden markieren?')
            ->modalDescription('Möchten Sie alle ausgewählten Episoden als vorhanden markieren?')
            ->modalSubmitActionLabel('Als vorhanden markieren')
            ->action(function (Collection $records) {
                $count = $records->count();

                $records->each(function (Episode $record) {
                    $record->owned = true;
                    $record->save();
                });

                Notification::make()
                    ->title('Episoden aktualisiert')
                    ->body("{$count} " . ($count === 1 ? 'Episode wurde' : 'Episoden wurden') . ' als vorhanden markiert.')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}


