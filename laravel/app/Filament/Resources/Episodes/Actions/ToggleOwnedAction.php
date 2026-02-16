<?php

namespace App\Filament\Resources\Episodes\Actions;

use App\Models\Episode;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ToggleOwnedAction
{
    public static function make(?string $name = null): Action
    {
        return Action::make($name ?? 'toggleOwned')
            ->label(fn(Episode $record) => $record->owned ? 'Als nicht vorhanden markieren' : 'Als vorhanden markieren')
            ->icon(fn(Episode $record) => $record->owned ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
            ->color(fn(Episode $record) => $record->owned ? 'danger' : 'success')
            ->requiresConfirmation()
            ->modalHeading(fn(Episode $record) => $record->owned ? 'Episode als nicht vorhanden markieren?' : 'Episode als vorhanden markieren?')
            ->modalDescription(fn(Episode $record) => $record->owned
                ? 'Möchten Sie diese Episode wirklich als nicht vorhanden markieren?'
                : 'Möchten Sie diese Episode wirklich als vorhanden markieren?'
            )
            ->modalSubmitActionLabel(fn(Episode $record) => $record->owned ? 'Nicht vorhanden' : 'Vorhanden')
            ->action(function (Episode $record) {
                $newStatus = !$record->owned;
                $record->owned = $newStatus;
                $record->save();

                Notification::make()
                    ->title('Status aktualisiert')
                    ->body($newStatus
                        ? 'Episode wurde als vorhanden markiert.'
                        : 'Episode wurde als nicht vorhanden markiert.'
                    )
                    ->success()
                    ->send();
            });
    }
}


