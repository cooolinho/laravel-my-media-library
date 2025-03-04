<?php

namespace App\Contracts\TheTVDBSchema;

abstract class BaseSchema
{
    protected array $rawData = [];

    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
    }

    public function toArray(): array
    {
        return $this->rawData;
    }
}
