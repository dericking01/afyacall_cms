<?php

namespace App\Jobs;

use App\Http\Helpers\DeliverySMS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDeliverySMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $destination = null;
    private $id = null;
    private $status = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($destination, $id, $status)
    {
        $this->destination = $destination;
        $this->id = $id;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	    $savedelivery = new DeliverySMS($this->destination);
        $savedelivery->smsDelivery($this->id, $this->status);
    }
}

