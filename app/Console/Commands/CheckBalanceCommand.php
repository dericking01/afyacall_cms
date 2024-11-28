<?php

namespace App\Console\Commands;

use App\Models\Opt;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log; // Import the Opt model

class CheckBalanceCommand extends Command
{
    // The name and signature of the command
    protected $signature = 'check:balance {productID} {msisdn}';

    // The command description
    protected $description = 'Check balance for a given productID and msisdn';

    // Execute the console command
    public function handle()
    {
        $productID = $this->argument('productID');
        $msisdn = $this->argument('msisdn');

        // Logging to ensure the command has started
        Log::info('checkbalance called with Product ID: ' . $productID . ' and MSISDN: ' . $msisdn);

        // Use Opt::getCode() from the Opt model
        $code = Opt::getCode();

        try {
            // Create a new Guzzle HTTP client
            $client = new Client();

            // Make the POST request to the API
            $response = $client->request('POST', 'https://197.250.9.191:23000/icg/query/balance/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'input_Username' => '921465',
                    'input_Password' => '5pmls4V!9]O]{IF',
                    'input_WASPShortcode' => '921465',
                    'input_ChannelType' => 'API',
                    'input_ProductID' => $productID,
                    'input_CustomerMSISDN' => $msisdn,
                    'input_OriginatorConversationID' => $code,
                ]
            ]);

            // Get the response body
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);

            // Log the full response from the API
            Log::info('Response from API: ' . json_encode($data));

            // Check if the response was successful
            if ($data['output_ResponseCode'] == '0') {
                $balance = $data['output_AirtimeBalance'];
                Log::info('Airtime Balance: ' . $balance); // Log the balance
                $this->info('Airtime Balance: ' . $balance); // Output to console
            } else {
                // Log non-successful response
                Log::info('Non-successful response: ' . json_encode($data));
                $this->error('No balance returned or error occurred.');
            }
        } catch (\Throwable $th) {
            // Log any errors or exceptions that occur
            Log::error('Error in checkbalance: ' . $th->getMessage());
            $this->error('Error: ' . $th->getMessage()); // Output error to console
        }
    }
}
