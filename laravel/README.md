# Laravel - my-media-library
## laravel-settings

generate a new settings class using this artisan command

```bash
    php artisan make:setting SettingName --group=groupName 
```
Now, add this settings class to the `settings.php` config file in the `settings` array, so it can be loaded by Laravel:

```php
    /*
     * Each settings class used in your application must be registered, you can
     * add them (manually) here.
     */
    'settings' => [
        GeneralSettings::class
    ],
```

default values that should be set in its migration. You can create a migration as such:
```
php artisan make:settings-migration CreateGeneralSettings
```
This command will create a new file in `database/settings` where you can add the properties and their default values:

```php
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Spatie');
        $this->migrator->add('general.site_active', true);
    }
}
```

migrate your database to add the properties:

```bash
php artisan migrate
```


## Filament
Publishing configuration
```bash
php artisan vendor:publish --tag=filament-config
```
Publishing translations
```bash
php artisan vendor:publish --tag=filament-panels-translations
```
To optimize Filament for production, you should run the following command in your deployment script:
```bash
php artisan filament:optimize
```

To clear the caches at once, you can run:
```bash
php artisan filament:optimize-clear
```
