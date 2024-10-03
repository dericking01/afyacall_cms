<?php

namespace App\Services;

use App\Jobs\ProcessLanguage;
use App\Models\Opt;
use App\Repositories\EnticementRepository;
use Illuminate\Support\Facades\Log;

class EnticementService
{

    protected $enticementRepository;

    public function __construct(EnticementRepository $enticementRepository)
    {
        $this->enticementRepository = $enticementRepository;
    }

    public function getAll()
    {
        return $this->enticementRepository->getAll();
    }


    public function getById($id)
    {
        return $this->enticementRepository->getById($id);
    }


    public function saveEnticementData($msisdn)
    {

        $code = Opt::getCode();
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'http://197.250.9.128:23000/icg/Enticement/', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'input_Username' => '102047',
                    'input_Password' => 'AnF301LeGXrq-pI12',
                    'input_WASPShortcode' => '102047',
                    'input_ProductID' => '102047_P01',
                    'input_CustomerMSISDN' => $msisdn,
                    'input_OriginatorConversationID' => $code,
                    'input_EnticementChannel' => 'USSDPush'
                ]
            ]);
            $results = $response->getBody()->getContents();
            $data = json_decode($results, true);
            Log::info($data);
            if ($data['output_ResponseCode'] == 0) {
                $checkifexist = $this->enticementRepository->checkifexists($msisdn);
                if ($checkifexist) {
                    $result = $this->enticementRepository->updatetheincrementnumber($msisdn);
                    return $result;
                }
                $result = $this->enticementRepository->save($msisdn);
                return $result;
            } elseif ($data['output_ResponseCode'] == -7) {
               //notifiy the customer 
               $sw = 'Tayari umejiunga na huduma hii';
               $en = 'You are already subscribed to this service';
               ProcessLanguage::dispatch($msisdn, $sw, $en);
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
