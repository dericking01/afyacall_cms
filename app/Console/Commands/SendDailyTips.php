<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BotSubscription;
use App\Models\Content;
use App\Http\Helpers\SmsHelper;
use Illuminate\Support\Facades\Log;

class SendDailyTips extends Command
{
    protected $signature = 'send:daily-tips';
    protected $description = 'Send daily SMS health tips to bot subscribers';

    public function handle()
    {
        $this->info('🚀 Starting to send daily tips to subscribers...');

        $messages = Content::pluck('message')->shuffle();
        $totalMessages = $messages->count();

        if ($totalMessages === 0) {
            $this->error('❌ No messages found in Content table.');
            return;
        }

        $messageIndex = 0;
        $smsHelper = new SmsHelper();
        $sentCount = 0;
        $usedMessages = [];

        BotSubscription::chunkById(100, function ($subscribers) use (&$messages, &$messageIndex, $smsHelper, &$sentCount, &$usedMessages) {
            foreach ($subscribers as $subscriber) {
                if (!isset($messages[$messageIndex])) {
                    $messageIndex = 0;
                    $messages = Content::pluck('message')->shuffle();
                }

                $msisdn = $subscriber->msisdn;
                $message = $messages[$messageIndex++];

                $smsHelper->SendDailyTips($msisdn, $message);
                $usedMessages[] = $message;
                $sentCount++;

                $logPayload = [
                    'msisdn'  => $msisdn,
                    'message' => $message,
                ];

                Log::info("📤 SMS Sent", ['payload' => json_encode($logPayload, JSON_PRETTY_PRINT)]);
                $this->info("📤 Sent SMS to: $msisdn");
            }
        });

        $summary = [
            'total_sms_sent'      => $sentCount,
            'unique_messages_used'=> count(array_unique($usedMessages)),
            'total_msisdns'       => $sentCount,
        ];

        $this->info('✅ All SMS messages sent successfully.');
        $this->info(json_encode($summary, JSON_PRETTY_PRINT));
        Log::info("✅ Daily Tips Summary", ['summary' => json_encode($summary, JSON_PRETTY_PRINT)]);
    }
}
