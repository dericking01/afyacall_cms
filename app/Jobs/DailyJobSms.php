<?php

namespace App\Jobs;

use App\Http\Helpers\SmsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DailyJobSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $cellNo = null;
    private $textMessage = null;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cellNo, $textMessage)
    {

        $this->cellNo = $cellNo;
        $this->textMessage = $textMessage;
    
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $smsHelper = new SmsHelper();
        $smsHelper->sendSms($this->cellNo, $this->textMessage);
    }
}
