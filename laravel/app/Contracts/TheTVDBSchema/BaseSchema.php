<?php

namespace App\Contracts\TheTVDBSchema;

use ReflectionClass;
use ReflectionClassConstant;

abstract class BaseSchema
{
    protected array $rawData = [];

    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
    }

    public function toArray(): array
    {
        $ref = new ReflectionClass(static::class);

        $values = [];
        foreach ($ref->getConstants(ReflectionClassConstant::IS_PUBLIC) as $constant => $value) {
            if (!array_key_exists($constant, $this->rawData)) {
                $values[$constant] = null;
                continue;
            }

            $values[$constant] = $this->rawData[$constant];
        }

        return $values;
    }
}
