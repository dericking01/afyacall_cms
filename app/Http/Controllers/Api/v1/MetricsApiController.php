<?php

namespace App\Http\Controllers\Api\v1;

use Throwable;
use Prometheus\RenderTextFormat;
use Prometheus\CollectorRegistry;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class MetricsApiController extends Controller
{
    protected $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->middleware('auth.metrics');
    }

    /**
     * @throws Throwable
     */
    public function index()
    {
        abort_if(
            Gate::denies('view_metrics'),
            403,
            'Unauthorized metrics access'
        );

        $renderer = new RenderTextFormat();
        $result = $renderer->render($this->registry->getMetricFamilySamples());

        return response($result, 200, [
            'Content-Type' => RenderTextFormat::MIME_TYPE
        ])->header('X-Metrics-Token', config('app.metrics_access_token'));
    }
}
