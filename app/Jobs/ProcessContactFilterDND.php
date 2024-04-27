<?php

namespace App\Jobs;

use App\Http\Helpers\ContactDNDFilter;
use App\Models\Blacklist;
use App\Models\Contact;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessContactFilterDND implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $dataArray = null;
    private $groupid = null;
    private $blacklistArray = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataArray,$blacklistArray, $groupid)
    {
        $this->dataArray = $dataArray;
        $this->groupid = $groupid;
        $this->blacklistArray = $blacklistArray;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $importData_arr = array();

        foreach ($this->dataArray as $key => $value) {

            $importData_arr[] = $value[0];
        }

        // $blacklisteds = Blacklist::pluck('msisdn')->toArray();
        Log::info('This is Imported data => ' . count($importData_arr));
        Log::info('this is blacklist => ' . count($this->blacklistArray));

        $results = array_diff($importData_arr, $this->blacklistArray);

        // $rnewresults = $this->new_array_diff($importData_arr, $blacklistArray);

        Log::info("this is the results " . count($results));
        foreach ($results as $key => $value) {
            $contacts[] = [
                'msisdn' => $value,
                'campaign_id' => $this->groupid,
            ];
        }
        Contact::insert($contacts);
        Log::info("insert data to database successfully ");
    }
}

