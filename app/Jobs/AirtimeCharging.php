<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Http\Helpers\ChargingHelper;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AirtimeCharging implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $amount;
    private $cellNo;
    private $service;
    private $category;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cellNo, $amount, $service, $category)
    {
        $this->amount = $amount;
        $this->cellNo = $cellNo;
        $this->service = $service;
        $this->category = $category;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $paymentHelper = new ChargingHelper();
        $paymentHelper->airtimecharging($this->cellNo, $this->amount, $this->service, $this->category);
    }
}
