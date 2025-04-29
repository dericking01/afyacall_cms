<?php

namespace App\Jobs;

use App\Models\BotSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class DeleteExpiredSubscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscriptionId;

    public function __construct($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    public function handle()
    {
        $subscription = BotSubscription::find($this->subscriptionId);

        if ($subscription && now()->greaterThanOrEqualTo($subscription->ends_at)) {
            $subscription->forceDelete(); // Permanently delete
        }

        Log::info('Subscription ID ' . $this->subscriptionId . ' deleted at scheduled time.');

    }
}
