<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\MetricsService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Prometheus\Exception\MetricsRegistrationException;

class PrometheusMetrics
{
    protected $metricsService;

    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function handle($request, Closure $next)
    {
        $startTime = microtime(true);

        /** @var Response $response */
        $response = $next($request);

        $duration = microtime(true) - $startTime;

        $this->metricsService->observeRequestDuration($duration, [
            $request->method() . ' - ' . $request->path(),
            $response->getStatusCode(),
            Auth::check() ? 'authenticated' : 'guest'
        ]);

        return $response;
    }
}
