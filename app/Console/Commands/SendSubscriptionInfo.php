<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Opt;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class SendSubscriptionInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:send-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send subscription information to the external API endpoint.';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        // Fetch all subscriptions where product_id = 2
        $subscriptions = Subscription::where('product_id', 2)
            ->with('customer') // Eager load the customer relationship
            ->get();
    
        foreach ($subscriptions as $subscription) {
            if (!$subscription->customer || !$subscription->customer->msisdn) {
                continue;
            }
    
            // Get the customer's MSISDN
            $msisdn = $subscription->customer->msisdn;
    
            // Generate the OriginatorConversation ID
            $originatorConversationId = (string) Opt::getCode() ?? 'fallback_convo_id'; // Ensure it's never blank
    
            // Log conversation ID for debugging
            Log::info("BOT Generated OriginatorConversation_id: \n" . json_encode(['id' => $originatorConversationId], JSON_PRETTY_PRINT));
    
            // Ensure it's not empty
            if (empty($originatorConversationId)) {
                // Log::error("BOT OriginatorConversation_id is missing for MSISDN: {$msisdn}");
                continue;
            }
    
            // Prepare the payload
            $payload = [
                'msisdn' => $msisdn,
                'starts_at' => $subscription->starts_at,
                'ends_at' => $subscription->ends_at,
                'OriginatorConversation_id' => $originatorConversationId,
            ];
    
            Log::info("Sending payload", $payload);
            Log::info("BOT Sending payload: \n" . json_encode($payload, JSON_PRETTY_PRINT));
    
            try {
                // Initialize Guzzle Client
                $client = new \GuzzleHttp\Client();
    
                // Send request using your strict format
                $response = $client->request('POST', 'https://192.168.1.200:443/api/v1/subscriptions/etl/sms', [
                    'verify' => false, // Ignore SSL verification if needed
                    'headers' => [
                        'Content-Type' => 'application/json',
                        // 'Authorization' => 'Bearer ' . $token, // Uncomment if needed
                    ],
                    'body' => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ]);
    
                // Get response content
                $statusCode = $response->getStatusCode();
                $responseBody = $response->getBody()->getContents();
    
                // Log::info("BOT Subscription info Response for {$msisdn}: \n" . json_encode($responseBody, JSON_PRETTY_PRINT));
            } catch (RequestException $e) {
                Log::error("BOT Failed to send subscription data for {$msisdn}: \n" . json_encode([
                    'error' => $e->getMessage(),
                    'request' => $e->getRequest()->getBody()->getContents() ?? 'N/A',
                    'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'No response'
                ], JSON_PRETTY_PRINT));
            }
        }
    
        Log::info('BOT All subscription data processing completed.');
    }

}