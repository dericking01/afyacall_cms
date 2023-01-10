<?php

namespace App\Jobs;

use App\Http\Helpers\ChargingHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AirtimeCharging implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $amount = null;
    private $cellNo = null;
    private $service = null;
    private $category = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cellNo, $amount,$service,$category)
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
        $paymentHelper->airtimecharging($this->cellNo, $this->amount,$this->service, $this->category);
    }
}
