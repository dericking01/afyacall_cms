<?php

namespace App\Http\Helpers;

use App\Models\BotCampaign;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BotCampaignHelper
{
    public static function updateStatus($msisdn, $newStatus)
    {
        $campaign = BotCampaign::where('msisdn', $msisdn)->first();

        if (!$campaign) {
            // Log::warning("No BotCampaign found for MSISDN: {$msisdn}");
            return false;
        }

        // If current status is '1', don't update
        if ($campaign->status === '1') {
            Log::info("BotCampaign status already '1' for MSISDN: {$msisdn} — skipping update.");
            return false;
        }

        // Proceed with update
        $campaign->status = $newStatus;
        $campaign->updated_at = Carbon::now();
        $campaign->save();

        Log::info("BotCampaign updated for MSISDN: {$msisdn} with status: {$newStatus}");
        return true;
    }
}
