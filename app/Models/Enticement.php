<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Enticement extends Model
{
    use HasFactory;
    protected $table = 'push_enticements';
    protected $fillable = [
        'product_id',
        'msisdn',
        'code',
        'status',
        'response_description',
        'requested_at',
        'completed_at',
        'channel',
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id','product_id');
    }

    public static function pushEnticement($phone, $productID,$id)
    {
        $code = Opt::getCode();

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://197.250.9.191:23000/icg/Enticement/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'input_Username' => '921465',
                    'input_Password' => '5pmls4V!9]O]{IF',
                    'input_WASPShortcode' => '921465',
                    'input_ProductID' => $productID,
                    'input_CustomerMSISDN' => $phone,
                    'input_OriginatorConversationID' => $code,
                    'input_EnticementChannel' => 'USSDPush'
                ]
            ]);

            $results = $response->getBody()->getContents();

            $data = json_decode($results, true);

            Log::info($data);

            //crete a record of this entricement
            Enticement::create([
                'product_id' => $id,
                'msisdn' => $phone,
                'code' => $code,
                'status' => $data['output_ResponseCode'],
                'response_description' => $data['output_ResponseDesc'],
                'requested_at' => now(),
                'completed_at' => $data['output_ResponseCode'] == 0 ? now() : null,
                'channel' => 'USSDPush', // Assuming the channel is always USSDPush, adjust if dynamic
            ]);

            return $data;
        } catch (\Throwable $th) {
            Log::error("There is an error on enticement ".$th->getMessage() . $phone);
            return [
                'output_ResponseCode' => 99,
                'output_ResponseDesc' => 'Error processing enticement '.$th->getMessage(),
            ];
        }
    }


    public static function promptEnticement($phone, $productID)
    {
        $code = Opt::getCode();

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://197.250.9.191:23000/icg/Enticement/', [
                'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'input_Username' => '921465',
                    'input_Password' => '5pmls4V!9]O]{IF',
                    'input_WASPShortcode' => '921465',
                    'input_ProductID' => $productID,
                    'input_CustomerMSISDN' => $phone,
                    'input_OriginatorConversationID' => $code,
                    'input_EnticementChannel' => 'USSDPush'
                ]
            ]);

            $results = $response->getBody()->getContents();

            $data = json_decode($results, true);

            Log::info($data);

            return $data;

        } catch (\Throwable $th) {
            Log::error("There is an error on PPenticement ".$th->getMessage() . $phone);
            return [
                'output_ResponseCode' => 99,
                'output_ResponseDesc' => 'Error processing enticement '.$th->getMessage(),
            ];
        }
    }
}
