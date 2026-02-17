<?php

namespace App\Jobs\Concerns;

use App\Models\JobLog;
use Illuminate\Database\Eloquent\Model;
use Throwable;

trait LogsJobActivity
{
    protected ?JobLog $jobLog = null;
    protected ?float $startTime = null;

    /**
     * Start logging the job
     */
    protected function logStart(?Model $loggable = null, ?string $message = null, array $context = []): JobLog
    {
        $this->startTime = microtime(true);

        $this->jobLog = JobLog::create([
            JobLog::job_class => static::class,
            JobLog::status => JobLog::STATUS_STARTED,
            JobLog::message => $message ?? 'Job gestartet',
            JobLog::context => $context,
            JobLog::loggable_type => $loggable ? get_class($loggable) : null,
            JobLog::loggable_id => $loggable?->id,
        ]);

        return $this->jobLog;
    }

    /**
     * Log successful job completion
     */
    protected function logSuccess(?string $message = null, array $context = []): void
    {
        if (!$this->jobLog) {
            return;
        }

        $this->jobLog->update([
            JobLog::status => JobLog::STATUS_SUCCESS,
            JobLog::message => $message ?? 'Job erfolgreich abgeschlossen',
            JobLog::context => array_merge($this->jobLog->context ?? [], $context),
            JobLog::finished_at => now(),
            JobLog::duration_seconds => $this->calculateDuration(),
        ]);
    }

    /**
     * Calculate job duration in seconds
     */
    private function calculateDuration(): ?float
    {
        if (!$this->startTime) {
            return null;
        }

        return round(microtime(true) - $this->startTime, 3);
    }

    /**
     * Log failed job
     */
    protected function logFailure(Throwable $exception, ?string $message = null, array $context = []): void
    {
        if (!$this->jobLog) {
            // Create a log entry if none exists
            $this->jobLog = JobLog::create([
                JobLog::job_class => static::class,
                JobLog::status => JobLog::STATUS_FAILED,
                JobLog::created_at => now(),
            ]);
        }

        $this->jobLog->update([
            JobLog::status => JobLog::STATUS_FAILED,
            JobLog::message => $message ?? 'Job fehlgeschlagen: ' . $exception->getMessage(),
            JobLog::context => array_merge($this->jobLog->context ?? [], $context),
            JobLog::exception => $this->formatException($exception),
            JobLog::finished_at => now(),
            JobLog::duration_seconds => $this->calculateDuration(),
        ]);
    }

    /**
     * Format exception for logging
     */
    private function formatException(Throwable $exception): string
    {
        return sprintf(
            "%s: %s in %s:%d\nStack trace:\n%s",
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
    }

    /**
     * Log skipped job
     */
    protected function logSkipped(?string $message = null, array $context = []): void
    {
        if (!$this->jobLog) {
            $this->jobLog = JobLog::create([
                JobLog::job_class => static::class,
                JobLog::status => JobLog::STATUS_SKIPPED,
                JobLog::created_at => now(),
            ]);
        }

        $this->jobLog->update([
            JobLog::status => JobLog::STATUS_SKIPPED,
            JobLog::message => $message ?? 'Job übersprungen',
            JobLog::context => array_merge($this->jobLog->context ?? [], $context),
            JobLog::finished_at => now(),
            JobLog::duration_seconds => $this->calculateDuration(),
        ]);
    }

    /**
     * Update log context during job execution
     */
    protected function logUpdate(array $context = []): void
    {
        if (!$this->jobLog) {
            return;
        }

        $this->jobLog->update([
            JobLog::context => array_merge($this->jobLog->context ?? [], $context),
        ]);
    }
}

