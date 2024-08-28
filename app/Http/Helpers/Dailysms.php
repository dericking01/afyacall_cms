<?php

namespace App\Http\Helpers;

use App\Jobs\DailyJobSms;
use App\Jobs\ProcessLanguage;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as FacadesLog;

class Dailysms
{

    function __construct()
    {
    }

    public function has_time_expire($number)
    {
        return true;
    }

    public function is_message_sent($message_sent_ids, $message_content_key)
    {

        $messagetype = ContentType::where('name', $message_content_key)->first();
        if ($messagetype) {
            $allmessage = Content::where('content_type', $messagetype['id'])->where('is_active', true)->get();
            if ($allmessage) {
                foreach ($allmessage->sortBy('length') as $value) {
                    if (!in_array($value->id, $message_sent_ids)) {
                        return  $value->id;
                    } else {
                        //without message keyword
                        $allmessagess = Content::where('is_active', true)->get();
                        foreach ($allmessagess->sortBy('length') as $value) {
                            if (!in_array($value->id, $message_sent_ids)) {
                                return  $value->id;
                            }
                        }
                    }
                }
            } else {
                $allmessagesss = Content::where('is_active', true)->get();
                foreach ($allmessagesss->sortBy('length') as $value) {
                    if (!in_array($value->id, $message_sent_ids)) {
                        return  $value->id;
                    }
                }
            }
        } else {
            $allmessageall = Content::where('is_active', true)->get();
            foreach ($allmessageall->sortBy('length') as $value) {
                if (!in_array($value->id, $message_sent_ids)) {
                    return  $value->id;
                }
            }
        }
    }

    public function is_message_sent2($message_sent_ids, $message_content_key)
    {
        $messagetype = ContentType::where('name', $message_content_key)->first();

        // Retrieve all active messages
        $query = Content::where('is_active', true);

        if ($messagetype) {
            // If a message content key is provided, filter by content type
            $query->where('content_type', $messagetype['id']);
        }

        // Get all messages that meet the criteria and sort them by length
        $allmessages = $query->orderBy('length')->get();

        foreach ($allmessages as $message) {
            if (!in_array($message->id, $message_sent_ids)) {
                return $message->id;
            }
        }

        // If no suitable message is found, return null or an appropriate value
        return null;
    }


    public function dailysmssent($msisdn, $content)
    {

        //check if the time of subs has  expire
        if ($this->has_time_expire($msisdn)) {
            //check the last send message
            $last_sms_send = Log::last_message_sent($msisdn);
            if ($last_sms_send) {
                $next_sms_send = $this->is_message_sent($last_sms_send, $content);
                // FacadesLog::info($next_sms_send);
                if ($next_sms_send) {
                    //dispatch message
                   DailyJobSms::dispatchSync($msisdn, $next_sms_send);
                } else {

                    DB::table('customers')
                        ->where('msisdn', $msisdn)
                        ->update(['content' => null]);
                    $sw = 'Message for this user has finished ' . $msisdn;
                    $en = 'Message for this user has finished ' . $msisdn;
                    ProcessLanguage::dispatchSync('255746805383', $sw, $en);
                }
            }
        }
    }
}

