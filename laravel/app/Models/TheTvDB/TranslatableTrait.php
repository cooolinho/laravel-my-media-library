<?php

namespace App\Models\TheTvDB;

use App\Settings\TheTvDbSettings;
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
        $settings = new TheTvDbSettings();
        $locale = $settings->languageDefault;
        $translations = $this->getTranslationsAttribute();

        try {
            return $translations[$locale][$property];
        } catch (Exception $e) {
            // if no translations found return empty string
            if (empty($translations)) {
                return '';
            }

            // try to find translation with fallback locale
            $fallback = $translations[TheTvDbSettings::LANGUAGE_FALLBACK][$property] ?? '';
            if (!empty($fallback)) {
                return $fallback;
            }

            // try to return first element in translations
            return $translations[array_key_first($translations)][$property] ?? '';
        }
    }
}
