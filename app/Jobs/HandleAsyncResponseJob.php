<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HandleAsyncResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    // Constructor to pass response data
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        try {
            $data = $this->data; // Get response data from the job's constructor

            // Ensure response contains necessary keys
            if (!isset($data['output_OriginatorConversationID']) || !isset($data['output_ResponseCode'])) {
                // Log::error(" BOT Invalid async response received:\n" . json_encode($data, JSON_PRETTY_PRINT));
                return;
            }

            // Extract OriginatorConversationID
            $originatorID = $data['output_OriginatorConversationID'];

            // Check if it starts with 'a255c'
            if (str_starts_with($originatorID, 'a255c')) {
                // Remove 'a255c' to restore the original ID
                $cleanedoriginatorID = substr($originatorID, strlen('a255c'));

                // Clone the original $data to avoid modifying it directly
                $forwardData = $data;

                // Override only the OriginatorConversationID (if cleaned)
                $forwardData['output_OriginatorConversationID'] = $cleanedoriginatorID;

                // Log before sending (formatted JSON)
                Log::info("BOT Forwarding Async Response to Docker:\n" . json_encode($forwardData, JSON_PRETTY_PRINT));

                // Send callback to Docker server using Guzzle
                $client = new \GuzzleHttp\Client();
                $response = $client->request('POST', 'https://192.168.1.200:443/api/v1/billing/icg/callback', [
                    'verify' => false,
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => json_encode($forwardData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ]);

                // Log the response from the server
                Log::info("BOT Docker Server Response:\n" . json_encode([
                    'status' => $response->getStatusCode(),
                    'body' => json_decode($response->getBody()->getContents(), true)
                ], JSON_PRETTY_PRINT));
            }

        } catch (\Throwable $th) {
            Log::error("BOT Error processing async response:\n" . json_encode([
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ], JSON_PRETTY_PRINT));
        }
    }
}
