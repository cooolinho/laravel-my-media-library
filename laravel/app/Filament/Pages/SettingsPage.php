<?php

namespace App\Filament\Pages;

use App\Settings\JobSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Spatie\LaravelSettings\Settings;

class SettingsPage extends Page implements HasForms
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Einstellungen';
    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.settings';
    protected static ?string $title = 'Einstellungen';

    public array $settings = [];
    const FORM_INPUT = 'settings';

    public function mount(JobSettings $jobsSettings): void
    {
        $this->form->fill([
            self::FORM_INPUT => [
                $jobsSettings::group() => $jobsSettings->toArray(),
            ],
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Jobs')
                ->schema(JobSettings::getFormSchema(self::FORM_INPUT))
                ->columns(1),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('Speichern')
                ->submit('submit')
                ->label('Einstellungen speichern'),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState()[self::FORM_INPUT];
        $this->saveSettings(new JobSettings(), $data);

        Notification::make()
            ->title(__('Einstellungen gespeichert!'))
            ->success()
            ->send();
    }

    private function saveSettings(Settings $settings, array $data): void
    {
        $values = $data[$settings::group()] ?? [];
        foreach ($values as $property => $value) {
            if (!property_exists($settings, $property)) {
                continue;
            }

            $settings->$property = $value;
        }

        $settings->save();
    }
}
