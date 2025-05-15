<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class SmsCampaignService
{
    public function pushCampaign(array $data): array
    {
        try {
            Log::info($data);

            $msisdns = [];

            $campaign = new Campaign();
            $campaign->name = $data['compaignname'];
            $campaign->message = $data['message'];
            $campaign->created_by = Auth::id();
            $campaign->save();

            $groups = Group::with('contact')->whereIn('id', $data['groups'])->get();

            foreach ($groups as $group) {
                foreach ($group->contact as $contact) {
                    $msisdn = $this->sanitizeMsisdn($contact->msisdn);
                    if ($this->isValidMsisdn($msisdn)) {
                        Log::info($msisdn);
                        $this->sendSmsToKannel($msisdn, $data['message']);
                        $msisdns[] = $msisdn;
                    }
                }
            }

            Log::info('All SMS pushed. Total: ' . count($msisdns));
            Log::info($msisdns);

            return [
                'count' => count($msisdns),
                'recipients' => $msisdns,
            ];

        } catch (\Exception $e) {
            Log::error("pushCampaign() failed: " . $e->getMessage());

            // Always return array to match method signature
            return [
                'count' => 0,
                'recipients' => [],
                'error' => $e->getMessage()
            ];
        }
    }


    private function sendSmsToKannel(string $msisdn, string $message): void
    {
        try {
            $client = new Client();
            $client->request('GET', 'http://192.168.1.200:6013/cgi-bin/sendsms', [
                'query' => [
                    'username'  => 'afya',
                    'password'  => 'Afya4017',
                    'from'      => '15723',
                    'to'        => $msisdn,
                    'text'      => $message,
                    'dlr-mask'  => 31,
                    'dlr-url'   => "http://192.168.1.10/api/sms/dailydeliveryreport?id={$msisdn}&status=%d",
                ],
                'timeout' => 2,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed SMS to {$msisdn}: " . $e->getMessage());
        }
    }

    private function sanitizeMsisdn(string $msisdn): string
    {
        $msisdn = preg_replace('/[^0-9]/', '', $msisdn);

        if (strlen($msisdn) == 9) {
            return '255' . $msisdn;
        }

        if (strlen($msisdn) == 10 && $msisdn[0] === '0') {
            return '255' . substr($msisdn, 1);
        }

        return $msisdn;
    }

    private function isValidMsisdn(string $msisdn): bool
    {
        return preg_match('/^2557[0-9]{8}$/', $msisdn);
    }
}
