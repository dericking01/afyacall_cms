<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Events\SubscriptionUpdated;
use App\Models\Subscription;
use App\Models\Opt;

class SendSubscriptionToThirdParty implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SubscriptionUpdated $event)
    {
        $originatorConversationId = (string) Opt::getCode();

        // Ensure the subscription has product_id = 2 and a customer relationship
        $subscription = Subscription::where('id', $event->subscription->id)
            ->where('product_id', 2)
            ->with('customer')
            ->first();

        if (!$subscription || !$subscription->customer || !$subscription->customer->msisdn) {
            // Log::info("BOT: Subscription does not meet criteria for sending.");
            return;
        }

        // Prepare payload
        $payload = [
            'msisdn' => $subscription->customer->msisdn,
            'starts_at' => $subscription->starts_at,
            'ends_at' => $subscription->ends_at,
            'OriginatorConversation_id' => $originatorConversationId,
        ];

        Log::info("BOT Sending SubsInfo payload: \n" . json_encode($payload, JSON_PRETTY_PRINT));


        try {
            $client = new Client();
            $response = $client->post('https://192.168.1.200:443/api/v1/subscriptions/etl/sms', [
                'verify' => false,
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $payload,
            ]);

        } catch (\Exception $e) {
            Log::error("BOT Failed to send subscription: ", [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
