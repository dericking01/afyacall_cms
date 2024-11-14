<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Opt;

class AIController extends Controller
{
    public function fromkannel(Request $request)
    {

        Log::info($request->all());
        // Define authorized senders
        $authorizedSenders = [
            '+255746805383', '+255757436283', '+255743054983', '+255754710702',
            '+255746193050', '+255746598695', '+255762332491', '+255743956595',
            '+255746088031', '+255756532635', '+255745994671', '+255765975152',
            '+255747234197', '+255754710722', '+255754710005', '+255747778248',
            '+255767455554', '+255745918839', '+255755103366', '+255753340734',
            '+255769168841', '+255769005969', '+255743305282', '+255756308638',
            '+255755887901', '+255769168841', '+255752820234', '+255759507610',
            '+255757436283', '+255754775072', '+255767196132', '+255786335594',
            '+255747616429', '+255754445850', '+255757762886', '+255763241440',
            '+255757436283', '+255764366696', '+255756937536', '+255764223986',
            '+255756308638', '+255753744714', '+255755980333', '+255745008573',
            '+255762332491', '+255745994671', '+255764930534', '+255761439331',
            '+255768142406', '+255756830126', '+255758178165', '+255755342708',
            '+255754515956', '+255766614688', '+255743304731', '+255766334243',
            '+255757472424', '+255766857868', '+255768326796', '+255765545250',
            '+255752519208', '+255757490210', '+255757490210', '+255765000112',
            '+255767281851', '+255767281851', '+255755185361', '+255744173668',
            '+255763285448', '+255743844528', '+255761559696', '+255761350499',
            '+255769888868', '+255745346783', '+255749955988'
        ];
        if (in_array(strtolower($request->sender), $authorizedSenders)) {
            $token = $this->getAuthToken();
            $code = Opt::getCode();
            if ($token) {
                try {
                        $client = new \GuzzleHttp\Client();
                        $response = $client->request('POST', 'https://192.168.1.212/api/v1/chat', [
                            'verify' => false,
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'Authorization' => 'Bearer ' . $token,
                            ],
                            'json' => [
                                'sender' => $request->sender,
                                'conversationId' => $code,
                                'message' => $request->text
                            ]
                        ]);
                        $results = $response->getBody()->getContents();
                        $data = json_decode($results, true);

                        Log::info($data);

                        //check if its successfull

                        $this->sendSmsResponse($request->sender, $data['response']);


                        return true;
                    } catch (\Throwable $th) {
                        Log::error("there is an error on redirect to AI");
                        Log::error($th->getMessage());
                    }
                }
            }
        }

        public function getAuthToken() 
        {
            $clientId = 'afyacall';
            $clientSecret = 'secret';
            $authString = base64_encode($clientId . ':' . $clientSecret);
            try {
                $client = new \GuzzleHttp\Client();
                $response = $client->request('POST', 'https://192.168.1.212/oauth2/token', [
                    'verify' => false,
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Authorization' => 'Basic ' . $authString,
                    ],
                    'form_params' => [
                        'grant_type' => 'client_credentials'
                    ]
                ]);
        
                $data = json_decode($response->getBody(), true);
                return $data['access_token'] ?? null;

            } catch (RequestException $e) {
                Log::error($e->getMessage());
                return null;
            }
        }

        public function sendSmsResponse($msisdn, $message)
        {
            try {
                    $client = new \GuzzleHttp\Client();
                    $client->request('GET', 'http://192.168.1.10:6013/cgi-bin/sendsms', [
                        'query' => [
                            'username' => 'afya',
                            'password' => 'Afya4017',
                            'from' => '15723',
                            'to' => $msisdn,
                            'text' => $message,
                        ]
                    ]);

            } catch (\Throwable $e) {
                Log::error($e->getMessage());
                return null;
            }
        }
}
