<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\BotSubscription;
use Carbon\Carbon;

class DeleteExpiredBotSubscriptions extends Command
{
    protected $signature = 'subscriptions:delete-expired';
    protected $description = 'Permanently delete all expired bot subscriptions where ends_at <= now()';

    public function handle()
    {
        Log::info('🧹 Running DeleteExpiredBotSubscriptions command...');

        // Use UTC for consistency with DB timestamps
        $now = Carbon::now('Africa/Dar_es_Salaam');
        Log::info('Current time: ' . $now->toDateTimeString());

        // Fetch records to delete
        $expiredSubscriptions = BotSubscription::where('ends_at', '<=', $now)->get();
        $count = $expiredSubscriptions->count();

        Log::info("Found {$count} expired records to delete.");

        // Permanently delete each record
        foreach ($expiredSubscriptions as $subscription) {
            Log::info("Deleting subscription ID {$subscription->id} with ends_at {$subscription->ends_at}");
            $subscription->forceDelete();
        }

        Log::info("✅ Deleted {$count} expired subscriptions.");
        $this->info("✅ Deleted {$count} expired subscriptions.");
    }
}
