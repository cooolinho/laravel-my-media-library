<?php

namespace App\Filament\Pages;

use App\Settings\FormSchemaInterface;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Spatie\LaravelSettings\Settings;

class SettingsPage extends Page implements HasForms
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.settings';
    protected static ?string $title = 'Settings';

    public array $settings = [];
    const FORM_INPUT = 'settings';

    /**
     * @var array<Settings>
     */
    private array $classes = [];

    public function __construct()
    {
        $config = config('settings.settings', []);
        foreach ($config as $class) {
            $this->classes[] = new $class();
        }
    }

    public function mount(): void
    {
        $formInput = [];
        foreach ($this->classes as $settings) {
            $formInput[$settings::group()] = $settings->toArray();
        }

        $this->form->fill([
            self::FORM_INPUT => $formInput,
        ]);
    }

    protected function getFormSchema(): array
    {
        $sections = [];
        foreach ($this->classes as $settings) {
            if ($settings instanceof FormSchemaInterface) {
                $sections[$settings::group()] = self::loadSettingsFormSchema($settings);
            }
        }

        $formSchemas = [];
        foreach ($sections as $heading => $schema) {
            $formSchemas[] = Section::make(Str::pascal($heading))
                ->schema($schema)
            ;
        }

        return $formSchemas;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('submit')
                ->submit('submit')
                ->label('Save'),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState()[self::FORM_INPUT];

        foreach ($this->classes as $settings) {
            $this->saveSettings($settings, $data);
        }

        Notification::make()
            ->title(__('Saved!'))
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

    private function loadSettingsFormSchema(FormSchemaInterface $settings): array
    {
        $fields = $settings::getFormSchema();

        /** @var Field $field */
        foreach ($fields as $field) {
            $prefix = sprintf('%s.%s.', self::FORM_INPUT, $settings::group());
            $originalStatePath = $field->getStatePath(false);
            $field->statePath($prefix . $originalStatePath);
        }

        return $fields;
    }
}
