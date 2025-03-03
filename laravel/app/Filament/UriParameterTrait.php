<?php

namespace App\Filament;

trait UriParameterTrait
{
    public function mount(): void
    {
        $this->handleUri();
    }
    
    protected function handleUri(): void
    {
        $model = static::getModel();
        foreach (request()->all() as $query => $value) {
            if (!in_array($query, (new $model)->getFillable())) {
                continue;
            }

            $this->data[$query] = $value;
        }
    }
}
