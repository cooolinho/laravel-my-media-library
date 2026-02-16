<?php

namespace App\Filament\Resources\Episodes\Actions;

use App\Models\Episode;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class EditNotesAction
{
    public static function make(?string $name = null): Action
    {
        return Action::make($name ?? 'editNotes')
            ->label('Notizen bearbeiten')
            ->icon('heroicon-o-pencil-square')
            ->color('gray')
            ->modalHeading('Notizen bearbeiten')
            ->modalDescription('Bearbeiten Sie die Notizen für diese Episode.')
            ->modalSubmitActionLabel('Speichern')
            ->modalWidth('2xl')
            ->schema([
                Textarea::make('notes')
                    ->label('Notizen')
                    ->rows(5)
                    ->maxLength(65535)
                    ->placeholder('Fügen Sie hier Ihre Notizen hinzu...')
                    ->helperText('Verwenden Sie dieses Feld für persönliche Anmerkungen zur Episode.')
            ])
            ->fillForm(fn(Episode $record): array => [
                'notes' => $record->notes,
            ])
            ->action(function (Episode $record, array $data): void {
                $record->notes = $data['notes'];
                $record->save();

                Notification::make()
                    ->title('Notizen gespeichert')
                    ->body('Die Notizen wurden erfolgreich aktualisiert.')
                    ->success()
                    ->send();
            });
    }
}

