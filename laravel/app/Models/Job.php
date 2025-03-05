<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $queue
 * @property string $payload
 * @property int $attempts
 * @property Carbon $reserved_at
 * @property Carbon $available_at
 * @property Carbon $created_at
 */
class Job extends Model
{
    // properties
    const id = 'id';
    const queue = 'queue';
    const payload = 'payload';
    const attempts = 'attempts';
    const reserved_at = 'reserved_at';
    const available_at = 'available_at';
    const created_at = 'created_at';

    public static function findByJobAndModelId(string $jobClass, int $modelId): Collection
    {
        return self::query()
            ->jobClass($jobClass)
            ->modelId($modelId)
            ->get();
    }

    public static function findByJob(string $jobClass): Collection
    {
        return self::query()
            ->jobClass($jobClass)
            ->get();
    }

    /**
     * magic scope method called via self::query()->jobClass($jobClass)
     *
     * @param Builder $query
     * @param string $jobClass
     * @return void
     */
    public static function scopeJobClass(Builder $query, string $jobClass): void
    {
        $query->where(self::payload, 'LIKE', '%' . sprintf('"commandName":"%s"', self::formatClassName($jobClass)) . '%');
    }

    /**
     * magic scope method called via self::query()->scopeModelId($id)
     *
     * @param Builder $query
     * @param int $id
     * @return void
     */
    public static function scopeModelId(Builder $query, int $id): void
    {
        $query->where(Job::payload, 'LIKE', '%"id%";i:' . $id . '%');
    }

    /**
     * converts "apple\banana\grape" ->"apple%banana%grape"
     *
     * @param string $class
     * @return string
     */
    private static function formatClassName(string $class): string
    {
        return Str::of($class)->explode('\\')->join('%');
    }
}
