<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BotSubscription;
use App\Jobs\ProcesSendDailySMS;

class SendDailyTips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan send:daily-tips
     */
    protected $signature = 'send:daily-tips';

    /**
     * The console command description.
     */
    protected $description = 'Send daily SMS health tips to bot subscribers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting to send daily tips to subscribers...');

        BotSubscription::chunkById(100, function ($subscribers) {
            foreach ($subscribers as $subscriber) {
                $msisdn = $subscriber->msisdn;
                $content = $subscriber->content;

                ProcesSendDailySMS::dispatch($msisdn, $content);

                $this->info("Queued SMS for: $msisdn");
            }
        });

        $this->info('✅ All SMS dispatches queued successfully.');
    }
}
