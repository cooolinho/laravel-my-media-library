<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property int $id
 * @property string $queue
 * @property array $payload
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

    // scopes
    const SCOPE_JOB = 'jobClass';
    const SCOPE_RECORD_ID = 'recordId';
    const SCOPE_RECORD_IDS = 'multipleRecords';

    // payload data
    const PAYLOAD_UUID = 'uuid';
    const PAYLOAD_DISPLAY_NAME = 'displayName';
    const PAYLOAD_JOB = 'job';
    const PAYLOAD_FAIL_ON_TIMEOUT = 'failOnTimeout';
    const PAYLOAD_DATA = 'data';
    const PAYLOAD_DATA_COMMAND_NAME = 'commandName';
    const PAYLOAD_DATA_COMMAND = 'command';

    /**
     * @return string[]
     */
    public function casts(): array
    {
        return [
            self::available_at => 'datetime',
            self::payload => 'array',
        ];
    }

    /**
     * @param string $jobClass
     * @return Collection
     */
    public static function findByJob(string $jobClass): Collection
    {
        return self::query()
            ->scopes([
                self::SCOPE_JOB => $jobClass,
            ])
            ->get();
    }

    /**
     * @param string $jobClass
     * @param int $recordId
     * @return Collection
     */
    public static function findByJobAndRecordId(string $jobClass, int $recordId): Collection
    {
        return self::query()
            ->scopes([
                Job::SCOPE_JOB => $jobClass,
                Job::SCOPE_RECORD_ID => $recordId,
            ])
            ->get();
    }

    /**
     * @param string $jobClass
     * @param array $recordIds
     * @return Collection
     */
    public static function findByJobAndRecordIds(string $jobClass, array $recordIds): Collection
    {
        return self::query()
            ->scopes([
                Job::SCOPE_JOB => $jobClass,
            ])
            ->recordIds($recordIds)
            ->get();
    }

    /**
     * magic scope method called via
     *
     * self::query()->jobClass("App\Jobs\SeriesDataJob")
     * or
     * self::query()->scopes([self::SCOPE_JOB => "App\Jobs\SeriesDataJob"])
     *
     * @param Builder $query
     * @param string $jobClass
     * @return void
     */
    public static function scopeJobClass(Builder $query, string $jobClass): void
    {
        $path = [self::payload, self::PAYLOAD_DATA, self::PAYLOAD_DATA_COMMAND_NAME];
        $query->where(Arr::join($path, '->'), '=', $jobClass);
    }

    /**
     * magic scope method called via
     *
     * self::query()->recordId(1)
     * or
     * self::query()->scopes([self::SCOPE_RECORD_ID => 1])
     *
     * @param Builder $query
     * @param int $recordId
     * @return void
     */
    public static function scopeRecordId(Builder $query, int $recordId): void
    {
        $path = [self::payload, self::PAYLOAD_DATA, self::PAYLOAD_DATA_COMMAND];
        $query->where(Arr::join($path, '->'), 'like', '%;i:' . $recordId . ';%');
    }


    /**
     * magic scope method called via
     *
     * self::query()->recordIds([1, 2, 3])
     *
     * @param Builder $query
     * @param array $recordIds
     * @return void
     */
    public static function scopeRecordIds(Builder $query, array $recordIds): void
    {
        $path = [self::payload, self::PAYLOAD_DATA, self::PAYLOAD_DATA_COMMAND];
        $query->where(function ($query) use ($recordIds, $path) {
            foreach ($recordIds as $recordId) {
                $query->orWhere(Arr::join($path, '->'), 'like', '%;i:' . $recordId . ';%');
            }
        });
    }

    /**
     * @return string
     */
    public function getCommandNameAttribute(): string
    {
        return Arr::get($this->payload, self::PAYLOAD_DATA . '.' . Job::PAYLOAD_DATA_COMMAND_NAME);
    }

    /**
     * @return string
     */
    public function getUuidAttribute(): string
    {
        return Arr::get($this->payload, self::PAYLOAD_UUID);
    }

    /**
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        return Arr::get($this->payload, self::PAYLOAD_DISPLAY_NAME);
    }

    /**
     * @return string
     */
    public function getJobAttribute(): string
    {
        return Arr::get($this->payload, self::PAYLOAD_JOB);
    }

    /**
     * @return string
     */
    public function getFailOnTimeoutAttribute(): string
    {
        return Arr::join($this->payload, self::PAYLOAD_FAIL_ON_TIMEOUT);
    }
}
