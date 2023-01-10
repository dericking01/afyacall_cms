<?php

namespace App\Jobs;

use App\Http\Helpers\Dailysms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcesSendDailySMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $msisdn = null;
    private $content = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($msisdn,$content)
    {
       $this->msisdn = $msisdn;
       $this->content = $content;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sendsmsdaily = new Dailysms();
        $sendsmsdaily->dailysmssent($this->msisdn,$this->content);
    }
}

