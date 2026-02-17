<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $job_class
 * @property string $status
 * @property string|null $message
 * @property array|null $context
 * @property string|null $exception
 * @property string|null $loggable_type
 * @property int|null $loggable_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $finished_at
 * @property float|null $duration_seconds
 * @property Model|null $loggable
 */
class JobLog extends Model
{
    const string id = 'id';
    const string job_class = 'job_class';
    const string status = 'status';
    const string message = 'message';
    const string context = 'context';
    const string exception = 'exception';
    const string loggable_type = 'loggable_type';
    const string loggable_id = 'loggable_id';
    const string created_at = 'created_at';
    const string finished_at = 'finished_at';
    const string duration_seconds = 'duration_seconds';

    // Status constants
    const string STATUS_STARTED = 'started';
    const string STATUS_SUCCESS = 'success';
    const string STATUS_FAILED = 'failed';
    const string STATUS_SKIPPED = 'skipped';

    protected $fillable = [
        self::job_class,
        self::status,
        self::message,
        self::context,
        self::exception,
        self::loggable_type,
        self::loggable_id,
        self::finished_at,
        self::duration_seconds,
    ];

    protected $casts = [
        self::context => 'array',
        self::created_at => 'datetime',
        self::finished_at => 'datetime',
        self::duration_seconds => 'float',
    ];

    /**
     * Polymorphic relation to Series, Episode, etc.
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get a human-readable job name
     */
    public function getJobNameAttribute(): string
    {
        $parts = explode('\\', $this->job_class);
        return end($parts);
    }

    /**
     * Check if job is still running
     */
    public function isRunning(): bool
    {
        return $this->status === self::STATUS_STARTED && $this->finished_at === null;
    }

    /**
     * Check if job was successful
     */
    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Check if job failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Get status badge color for Filament
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_STARTED => 'warning',
            self::STATUS_SUCCESS => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_SKIPPED => 'gray',
            default => 'gray',
        };
    }
}

