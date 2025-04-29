<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Opt;
use Carbon\Carbon;


class TestServiceInfoAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test ICG Service Info API';

    public function handle()
    {
        $client = new Client();
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $conversationId = Opt::getCode(); // Your custom function

        $payload = [
            'input_Username'             => '921465',
            'input_Password'             => '5pmls4V!9]O]{IF',
            'input_RequestType'          => 'Customer-Subscription',
            'input_WASPShortcode'        => '921465',
            'input_ProductID'            => '921465_P01',
            'input_CustomerMSISDN'       => '255743956595',
            'input_ConsentDateTime'      => $now,
            'input_ConsentChannel'       => 'API',
            'input_ChargePriority'       => 'Airtime',
            'input_OriginatorConversationID' => $conversationId,
        ];

        $this->info("Payload:\n" . json_encode($payload, JSON_PRETTY_PRINT));

        try {
            $response = $client->post('https://197.250.9.191:23000/icg/serviceInfo/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload
            ]);

            $body = json_decode($response->getBody(), true);

            $this->info("Response:\n" . json_encode($body, JSON_PRETTY_PRINT));
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->error("RequestException: " . $e->getMessage());
            if ($e->hasResponse()) {
                $errorBody = (string) $e->getResponse()->getBody();
                $this->error("Error Response:\n" . $errorBody);
            }
        } catch (\Exception $e) {
            $this->error("General Exception: " . $e->getMessage());
        }
    }
}
