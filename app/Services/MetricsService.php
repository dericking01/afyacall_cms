<?php

namespace App\Services;

use Prometheus\CollectorRegistry;
use Prometheus\Exception\MetricsRegistrationException;

class MetricsService
{
    protected $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function incrementQueueJobs(string $queue, string $status)
    {
        $counter = $this->registry->getOrRegisterCounter(
            config('prometheus.namespace'),
            'laravel_queue_jobs_total',
            'Total queue jobs processed',
            ['queue', 'status']
        );
        $counter->inc([$queue, $status]);
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function observeRequestDuration(float $duration, array $labels)
    {
        $histogram = $this->registry->getOrRegisterHistogram(
            config('prometheus.namespace'),
            'laravel_http_request_duration_seconds',
            'Laravel HTTP request duration',
            ['method', 'path', 'status'],
            [0.1, 0.5, 1, 2, 5]
        );

        $histogram->observe($duration, $labels);
    }
}
