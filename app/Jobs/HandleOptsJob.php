<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Opt;


class HandleOptsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data; // Add this line

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        Log::info("BOT Processing Vodacom Callback in Job:\n" . json_encode($this->data, JSON_PRETTY_PRINT));
        $code = Opt::getCode();

        $vodacomConversationID = $this->data['input_OriginatorConversationID'] ?? '';

        // Ensure data is properly received
        if (!$vodacomConversationID) {
            Log::error("BOT Received data but input_OriginatorConversationID is missing.");
            return;
        }

        // Lookup stored Docker convo ID
        $originalDockerConvoID = Cache::get("vodacom_convo:$vodacomConversationID");

        if (!$originalDockerConvoID) {
            Log::error("BOT No matching Docker convo ID found for Vodacom convo ID: " . $vodacomConversationID);
            return;
        }

        // Prepare response
        $dockerResponse = [
            "input_WASPShortcode" => $this->data['input_WASPShortcode'],
            "input_ProductID" => $this->data['input_ProductID'],
            "input_CustomerMSISDN" => $this->data['input_CustomerMSISDN'],
            "input_RequestType" => $this->data['input_RequestType'],
            "input_OriginatorConversationID" => $this->data['input_OriginatorConversationID'],
            "output_OriginatorConversationID" => $originalDockerConvoID,
        ];

        Log::info("BOT OPTS RES 2B SENT:\n". json_encode($dockerResponse, JSON_PRETTY_PRINT));

        try {
            $client = new Client();
            $response = $client->post('https://192.168.1.200:443/api/v1/billing/icg/callback', [
                'verify' => false,
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $dockerResponse
            ]);

            Log::info("BOT Sent Customer Enticement Response to Docker:\n" . json_encode([
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ], JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            Log::error("BOT Failed to send Enticement response to Docker: " . $e->getMessage());
        }
    }
}

