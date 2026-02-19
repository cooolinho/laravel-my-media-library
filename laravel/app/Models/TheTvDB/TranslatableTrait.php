<?php

namespace App\Models\TheTvDB;

use App\Settings\TheTVDBSettings;
use Exception;

trait TranslatableTrait
{
    const string name = 'name';
    const string overview = 'overview';
    const string has_many_translations = 'translations';

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
        // First try to get translations from relationship table
        if ($this->relationLoaded(static::has_many_translations)) {
            return $this->getTranslationsFromRelation();
        }

        // Load translations from relation if not already loaded
        return $this->getTranslationsFromRelation();
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
        $settings = new TheTVDBSettings();
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
            $fallback = $translations[TheTVDBSettings::LANGUAGE_FALLBACK][$property] ?? '';
            if (!empty($fallback)) {
                return $fallback;
            }

            // try to return first element in translations
            return $translations[array_key_first($translations)][$property] ?? '';
        }
    }

    /**
     * Get translations from the relationship table
     */
    private function getTranslationsFromRelation(): array
    {
        $relationName = static::has_many_translations;

        if (!method_exists($this, $relationName)) {
            return [];
        }

        $translations = $this->$relationName()->get();
        $result = [];

        foreach ($translations as $translation) {
            $result[$translation->lang] = [
                static::name => $translation->name,
                static::overview => $translation->overview,
            ];
        }

        return $result;
    }
}

