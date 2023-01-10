<?php

namespace App\Jobs;
use App\Services\EnticementService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WebsiteEnticement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $msisdn = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($msisdn)
    {
        $this->msisdn = $msisdn;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EnticementService $enticementService)
    {
       $enticementService->saveEnticementData($this->msisdn);
    }
}
