<?php

namespace App\Jobs;

use App\Http\Helpers\LanguageHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLanguage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $cellNo = null;
    private $sw = null;
    private $en = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cellNo,$sw,$en)
    {
        $this->cellNo = $cellNo;
        $this->sw = $sw;
        $this->en = $en;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $languageHelper = new LanguageHelper();
        $languageHelper->language($this->cellNo,$this->sw,$this->en);
    }
}
