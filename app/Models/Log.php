<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    public function content()
    {
        return $this->belongsTo(Content::class,'content_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public static function last_message_sent($number){

        $lastmessage = Log::where('last_sent', $number)->get()->pluck('content_id');
        if (!$lastmessage->isEmpty()) {
            return $lastmessage->toArray();
        } else {
            return array(0);
        }
    }
}
