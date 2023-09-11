<?php

namespace App\Jobs;

use App\Http\Helpers\BroadcastCampaign as HelpersBroadcastCampaign;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastCampaign implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $receipts = null;
    private $campaign_id = null;
    public $tries = 1;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($receipts,$campaign_id)
    {
        $this->receipts = $receipts;
        $this->campaign_id = $campaign_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $broadcasthelper = new HelpersBroadcastCampaign();
        $broadcasthelper->broadcast($this->receipts, $this->campaign_id);
    }
}
