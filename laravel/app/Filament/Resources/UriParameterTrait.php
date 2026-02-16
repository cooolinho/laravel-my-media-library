<?php

namespace App\Filament\Resources;

use Illuminate\Database\Eloquent\Model;

trait UriParameterTrait
{
    public function mount(): void
    {
        $this->fillFromUriParameters();
    }

    protected function fillFromUriParameters(): void
    {
        /** @var Model $model */
        $model = static::getModel();
        foreach (request()->all() as $query => $value) {
            if (!in_array($query, (new $model)->getFillable())) {
                continue;
            }

            $this->data[$query] = $value;
        }
    }
}
