<?php

namespace App\Models\TheTvDB;

use Exception;

trait TranslatableTrait
{
    public function getName(): string
    {
        return $this->getNameTranslationAttribute();
    }

    public function getOverview(): string
    {
        return $this->getOverviewTranslationAttribute();
    }

    public function getNameAttribute(): string
    {
        return $this->getName();
    }

    public function getOverviewAttribute(): string
    {
        return $this->getOverview();
    }

    public function getTranslationsAttribute(): array
    {
        return json_decode($this->attributes[static::translations], true, 512, JSON_OBJECT_AS_ARRAY);
    }

    public function getNameTranslationAttribute(): string
    {
        return $this->getTranslationProperty(static::name);
    }

    public function getOverviewTranslationAttribute(): string
    {
        return $this->getTranslationProperty(static::overview);
    }

    private function getTranslationProperty(string $property): string
    {
        $locale = config('app.locale');
        $fallbackLocale = config('app.fallback_locale');
        $mapping = config('app.thetvdb.locale_language_mapping');
        $translations = $this->getTranslationsAttribute();

        try {
            return $translations[$mapping[$locale]][$property];
        } catch (Exception $e) {
            return $translations[$mapping[$fallbackLocale]][$property] ?? '';
        }
    }
}
