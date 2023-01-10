<?php

namespace App\Jobs;

use App\Models\Blacklist;
use App\Models\Campaign;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBlacklistChecks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $msisdn = null;
    private $campaign_id = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($msisdn,$campaign_id)
    {
        $this->msisdn = $msisdn;
        $this->campaign_id = $campaign_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
          
        $blacklisted = Blacklist::where('msisdn', $this->msisdn)->get()->first();
        if (!$blacklisted) {
            $contact = new Contact();
            $contact->msisdn =  $this->msisdn;
            $contact->sent_status = 0;
            $contact->delivery_status = 0;
            $contact->senttimes = 0;
            $contact->campaign_id = $this->campaing_id;
            $contact->save();

             $this->prodcastsms($this->msisdn,$this->campaing_id);
        }

        
    }

    public function prodcastsms($number, $campaing_id)
    {
        $campaign = Campaign::where('id',$campaing_id)->get()->first();
        try {
            // $client = new \GuzzleHttp\Client();
            // $client->request('GET', 'http://192.168.1.10:6013/cgi-bin/sendsms', [
            //     'query' => [
            //         'username' => 'afya',
            //         'password' => 'Afya4017',
            //         'from' => 'AFYACALL',
            //         'to' => '+' . $number,
            //         'text' => $campaign->message,
            //     ]
            // ]);

            $count = $campaign->status;
            $campaign->status  = $count + 1;
            $campaign->save();
            Log::error('saved compaing ', $count);
            //update the number of success sms sent
            return true;
        } catch (\Throwable $e) {
            Log::error('failed to send sms' . $e->getMessage());

            return false;
        }
    }
}
