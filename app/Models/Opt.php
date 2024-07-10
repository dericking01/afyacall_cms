<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opt extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_ID','ConversationID','OriginatorConversationID','opt_value','product_ID','date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_ID');
    }

    public function product(){
 
        return $this->belongsTo(Product::class,'product_ID');
        
    }

    public static function getCode()
    {

        $number = 1;
        $characters = 'ewlsnmdkztpzqprtba';
        $pins = array();
        for ($j = 0; $j < $number; $j++) {
            $pin = mt_rand(1, 10000000000000)
                . mt_rand(1, 10000000000000)
                . $characters[rand(0, strlen($characters) - 1)];
            $string = str_shuffle($pin);
            $pin = Opt::where('ConversationID', '=', $string)->first();
            if ($pin) {
                $j--;
            } else {
                $pins[$j] = $string;
            }
        }
        $code = implode('', $pins);

        return $code;
    }

    public static function getServertime()
    {
        $localTime = new \DateTime("now", new \DateTimeZone('Africa/Dar_es_Salaam'));
        $serverTime = $localTime->format('Y-m-d H:i:s');
        return $serverTime;
    }


    public static function getTimestamp()
    {
        $localTime = new \DateTime("now", new \DateTimeZone('Africa/Dar_es_Salaam'));
        $serverTime = $localTime->format('Y-m-d H:i:s.u');
        return $serverTime;
    }
}
