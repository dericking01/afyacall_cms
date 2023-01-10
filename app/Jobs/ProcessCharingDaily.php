<?php

namespace App\Jobs;

use App\Http\Helpers\ChargingExistingCustomer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCharingDaily implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $product_ID = null;
    private $amount = null;
    private $cellNo = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product_ID,$cellNo, $amount)
    {
        $this->product_ID = $product_ID;
        $this->amount = $amount;
        $this->cellNo = $cellNo;


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $paymentHelper = new ChargingExistingCustomer($this->product_ID);
        $paymentHelper->charging($this->cellNo, $this->amount);
    }
}

