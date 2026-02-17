<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $endpoint
 * @property string $method
 * @property array|null $params
 * @property int|null $status_code
 * @property array|null $response_data
 * @property string|null $error_message
 * @property float|null $response_time
 * @property bool $success
 * @property bool $from_cache
 * @property string|null $bearer_token_hash
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * Scopes:
 * @method static Builder|TheTVDBApiLog successful()
 * @method static Builder|TheTVDBApiLog failed()
 * @method static Builder|TheTVDBApiLog forEndpoint(string $endpoint)
 * @method static Builder|TheTVDBApiLog forMethod(string $method)
 * @method static Builder|TheTVDBApiLog fromCache()
 * @method static Builder|TheTVDBApiLog today()
 * @method static Builder|TheTVDBApiLog lastDays(int $days)
 */
class TheTVDBApiLog extends Model
{
    const string id = 'id';
    const string endpoint = 'endpoint';
    const string method = 'method';
    const string params = 'params';
    const string status_code = 'status_code';
    const string response_data = 'response_data';
    const string error_message = 'error_message';
    const string response_time = 'response_time';
    const string success = 'success';
    const string from_cache = 'from_cache';
    const string bearer_token_hash = 'bearer_token_hash';
    const string created_at = 'created_at';
    const string updated_at = 'updated_at';

    protected $table = 'the_tvdb_api_logs';

    protected $fillable = [
        self::endpoint,
        self::method,
        self::params,
        self::status_code,
        self::response_data,
        self::error_message,
        self::response_time,
        self::success,
        self::from_cache,
        self::bearer_token_hash,
    ];

    protected $casts = [
        self::params => 'array',
        self::response_data => 'array',
        self::success => 'boolean',
        self::from_cache => 'boolean',
        self::created_at => 'datetime',
        self::updated_at => 'datetime',
    ];

    /**
     * Berechne die durchschnittliche Response Time für einen bestimmten Endpoint
     */
    public static function averageResponseTime(?string $endpoint = null): float
    {
        $query = static::query()->whereNotNull(self::response_time);

        if ($endpoint) {
            $query->where(self::endpoint, $endpoint);
        }

        return (float)$query->avg(self::response_time);
    }

    /**
     * Berechne die Erfolgsrate
     */
    public static function successRate(?string $endpoint = null): float
    {
        $query = static::query();

        if ($endpoint) {
            $query->where(self::endpoint, $endpoint);
        }

        $total = $query->count();

        if ($total === 0) {
            return 0;
        }

        $successful = $query->where(self::success, true)->count();

        return round(($successful / $total) * 100, 2);
    }

    /**
     * Scope für erfolgreiche Requests
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where(self::success, true);
    }

    /**
     * Scope für fehlgeschlagene Requests
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where(self::success, false);
    }

    /**
     * Scope für einen bestimmten Endpoint
     */
    public function scopeForEndpoint(Builder $query, string $endpoint): Builder
    {
        return $query->where(self::endpoint, $endpoint);
    }

    /**
     * Scope für eine bestimmte HTTP-Methode
     */
    public function scopeForMethod(Builder $query, string $method): Builder
    {
        return $query->where(self::method, strtoupper($method));
    }

    /**
     * Scope für Requests aus dem Cache
     */
    public function scopeFromCache(Builder $query): Builder
    {
        return $query->where(self::from_cache, true);
    }

    /**
     * Scope für Requests heute
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate(self::created_at, today());
    }

    /**
     * Scope für Requests in den letzten X Tagen
     */
    public function scopeLastDays(Builder $query, int $days): Builder
    {
        return $query->where(self::created_at, '>=', now()->subDays($days));
    }
}

