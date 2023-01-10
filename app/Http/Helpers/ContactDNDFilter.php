<?php

namespace App\Http\Helpers;

use App\Models\Blacklist;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;

class ContactDNDFilter
{
    function __construct()
    {
    }

    public function filterDND($msisdn, $groupid)
    {
        try {
            $blacklisteds = Blacklist::pluck('msisdn')->toArray();;

            $results = array_diff($msisdn, $blacklisteds);

            foreach ($results as $key => $contact) {
                //check data if it exit in Blacklists
                Log::info($contact);
                $contact = new Contact();
                $contact->msisdn = $msisdn;
                $contact->campaign_id = $groupid;
                $contact->save();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
