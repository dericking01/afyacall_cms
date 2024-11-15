<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\SmartBango;
use Illuminate\Http\Request;
use App\Services\CustomerService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log as FacadesLog;

class SMSController extends Controller
{
    public function receivedsmsfromkannel(Request $request, CustomerService $customerService)
    {
        //check the incoming requests and log into database
        FacadesLog::info($request);
        // if (in_array(strtolower($request->sender), ['+255757223687','+255745821232','+255745994671','+255766992111','+255747234197','+255744663450','+255754711547','+255747778248','+255767455554'])) {

        //     try {
        //         $client = new \GuzzleHttp\Client();
        //         $response = $client->request('POST', 'http://192.168.1.50:6000/api/sms/receivedsms', [
        //             'verify' => false,
        //             'headers' => [
        //                 'Content-Type' => 'application/json',
        //             ],
        //             'json' => [
        //                 'sender' => $request->sender,
        //                 'service' => $request->service
        //             ]
        //         ]);
        //         $results = $response->getBody()->getContents();
        //         $data = json_decode($results, true);

        //         return true;
        //     } catch (\Throwable $th) {
        //         FacadesLog::error("there is an error on redirect to UAT");
        //         FacadesLog::error($th->getMessage());
        //     }

        //     return true;
        // }
        //validate data

        //update to the smartbango tables
        if (in_array(strtolower($request->service), ['afyabango', 'afyasmart', 'afyasmartd', 'afyasmartdoc'])) {
            $smartBangoData = [
                'msisdn' => ltrim($request->sender, '+'),
                'keyword' => $request->service,
            ];
            SmartBango::create($smartBangoData);
        }
        if (strtolower($request->service) == 'afya') {
            return $customerService->subscribe_sms($request);
        } elseif (strtolower($request->service) == 'afyaivr') {
            return $customerService->subscribe_ivr($request);
        } elseif (strtolower($request->service) == 'ondoa') {
            return $customerService->unsubscribe_allservices($request);
        } elseif ($this->searchKeyword($request->service) == 'ondoaivr') {
            return $customerService->unsubscribe_ivr($request);
        } elseif (strtolower($request->service) == 'afyabango') {
            return $customerService->subscribe_sms($request);
        } elseif (strtolower($request->service) == 'afyasmart') {
            return $customerService->subscribe_ivr($request);
        } elseif (strtolower($request->service) == 'afyasmartd' || strtolower($request->service) == 'afyasmartdoc') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya1' || strtolower($request->service) == 'afya01') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya2' || strtolower($request->service) == 'afya02') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya3' || strtolower($request->service) == 'afya03') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya4' || strtolower($request->service) == 'afya04') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya5' || strtolower($request->service) == 'afya05') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya6' || strtolower($request->service) == 'afya06') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya7' || strtolower($request->service) == 'afya07') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya8' || strtolower($request->service) == 'afya08') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya9' || strtolower($request->service) == 'afya09') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya10' || strtolower($request->service) == 'afya010') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya11' || strtolower($request->service) == 'afya011') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya12' || strtolower($request->service) == 'afya012') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya13' || strtolower($request->service) == 'afya013') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya14' || strtolower($request->service) == 'afya014') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya15' || strtolower($request->service) == 'afya015') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya16' || strtolower($request->service) == 'afya016') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya17' || strtolower($request->service) == 'afya017') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya18' || strtolower($request->service) == 'afya018') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya19' || strtolower($request->service) == 'afya019') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afya20' || strtolower($request->service) == 'afya020') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (strtolower($request->service) == 'afyadoc') {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (preg_match('/^afya\s?(1[0-9]?|20)$/i', strtolower($request->service))) {
            return $customerService->subscribe_doctor_sub($request);
        } elseif (preg_match('/^afya\s?(2[1-9]?|30)$/i', strtolower($request->service))) {
            return $customerService->subscribe_sms($request);
        } elseif (preg_match('/^afya\s?(3[1-9]?|40)$/i', strtolower($request->service))) {
            return $customerService->subscribe_ivr($request);
        } elseif ($this->searchKeyword($request->service) == 'ondoadoc') {
            return $customerService->unsubscribe_doctor_subscription($request);
        } elseif ($this->searchKeyword($request->service) == 'ondoasms') {
            return $customerService->unsubscribe_sms($request);
        } else {
            return $customerService->subscribe_sms($request);
        }
    }

    public function searchKeyword($searchTerm)
    {
        $afyacallkeywords = ['ondoadoc', 'ondoaivr', 'ondoasms', 'afyaivr', 'afyasms', 'afyadoc'];

        $threshold = 0.8;
        $maxDistance = 5;

        $searchTerm = trim(strtolower($searchTerm));

        $closestafyacallkeyword = '';
        $closestSimilarity = 0;
        $closestDistance = PHP_INT_MAX;

        foreach ($afyacallkeywords as $afyacallkeyword) {
            $afyacallkeyword = trim(strtolower($afyacallkeyword));
            similar_text($afyacallkeyword, $searchTerm, $similarity);
            $levenshteinDistance = levenshtein($afyacallkeyword, $searchTerm);

            if ($similarity >= ($threshold * 100) || $levenshteinDistance <= $maxDistance) {
                if ($similarity > $closestSimilarity || ($similarity == $closestSimilarity && $levenshteinDistance < $closestDistance)) {
                    $closestafyacallkeyword = $afyacallkeyword;
                    $closestSimilarity = $similarity;
                    $closestDistance = $levenshteinDistance;
                }
            }
        }

        if ($closestafyacallkeyword !== '') {
            return $closestafyacallkeyword;
        } else {
            return $searchTerm;
        }
    }
}
