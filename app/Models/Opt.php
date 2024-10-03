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
        $characters = 'ewlsnmdkztpzqprtba';
        $batchSize = 100;  // Number of codes to generate in one batch
        $code = '';
        $foundUnique = false;
    
        while (!$foundUnique) {
            $potentialCodes = [];
    
            for ($i = 0; $i < $batchSize; $i++) {
                $pin = mt_rand(1000000000000, 9999999999999) . mt_rand(1000000000000, 9999999999999) . $characters[rand(0, strlen($characters) - 1)];
                $string = str_shuffle($pin);
                $potentialCodes[] = $string;
            }
    
            // Check uniqueness in bulk
            $existingCodes = Opt::whereIn('ConversationID', $potentialCodes)->pluck('ConversationID')->toArray();
            $uniqueCodes = array_diff($potentialCodes, $existingCodes);
    
            if (!empty($uniqueCodes)) {
                $code = reset($uniqueCodes);  // Get the first unique code
                $foundUnique = true;
            }
        }
    
        return $code;
    }

    public static function getCodeOld()
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

    public static function generateUUIDv1() 
    {

       // Get the current time in 100-nanosecond intervals since UUID epoch
       $time = microtime(true) * 10000000 + 0x01B21DD213814000;
       $timeHex = sprintf('%015x', $time);

       // Generate a random clock sequence
       $clockSeq = random_int(0, 0x3FFF);
       $clockSeqHex = sprintf('%04x', $clockSeq);

       // Generate a random 48-bit node (6 bytes)
       $node = bin2hex(random_bytes(6));

       // Add some more randomness to ensure uniqueness
       $randomBits = bin2hex(random_bytes(2)); // 2 bytes (16 bits) of randomness
       $node = substr_replace($node, $randomBits, -4, 4);

       // Combine the components into the UUID format
       $uuid = sprintf(
           '%08s-%04s-%04x-%04x-%012s',
           substr($timeHex, 0, 8),
           substr($timeHex, 8, 4),
           (0x1000 | (hexdec(substr($timeHex, 12, 4)) & 0x0FFF)),
           (0x8000 | ($clockSeq & 0x3FFF)),
           $node
       );

       return $uuid;
   
    }

}
