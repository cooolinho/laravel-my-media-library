<?php

namespace App\Console\Commands;

use App\Services\TheTVDBApiLogger;
use Illuminate\Console\Command;

class TheTVDBApiStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thetvdb:statistics {--days=7 : Number of days to analyze}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display TheTVDB API usage statistics';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int)$this->option('days');

        $this->info("TheTVDB API Statistics (Last {$days} days)");
        $this->line('');

        $stats = TheTVDBApiLogger::getStatistics($days);

        if (empty($stats)) {
            $this->warn('No statistics available.');
            return self::SUCCESS;
        }

        // Overall Statistics
        $this->info('Overall Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Requests', $stats['total_requests']],
                ['Successful Requests', $stats['successful_requests']],
                ['Failed Requests', $stats['failed_requests']],
                ['Cached Requests', $stats['cached_requests']],
                ['Success Rate', $stats['success_rate'] . '%'],
                ['Cache Hit Rate', $stats['cache_hit_rate'] . '%'],
                ['Avg Response Time', $stats['average_response_time'] . ' ms'],
            ]
        );

        $this->line('');

        // Endpoint Statistics
        if (!empty($stats['requests_by_endpoint'])) {
            $this->info('Statistics by Endpoint:');

            $endpointRows = [];
            foreach ($stats['requests_by_endpoint'] as $endpoint => $data) {
                $endpointRows[] = [
                    $endpoint,
                    $data['total'],
                    $data['successful'],
                    $data['failed'],
                    $data['avg_response_time'] . ' ms',
                ];
            }

            $this->table(
                ['Endpoint', 'Total', 'Success', 'Failed', 'Avg Response Time'],
                $endpointRows
            );
        }

        return self::SUCCESS;
    }
}

