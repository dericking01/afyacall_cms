<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\IPGCharging;
use App\Http\Helpers\IPGChargingCustomer;
use App\Models\Content;
use App\Models\Customer;
use App\Models\Log;
use App\SmsLog;
use App\Smscontent;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use SimpleXMLElement;
use SoapClient;
use App\Models\Rating;


class HomeController
{


    public function index()
    {

        $recentmesages = Log::with('content')
            ->with('customer')
            ->skip(0)->take(20)->latest()
            ->get();
        $contentcreats = Content::with('type')->skip(0)->take(4)->latest()->get();
	    $totalactive = Subscription::count();

        // return $percentage;
        $percentage_active_sub  = 100;
        $percentage_inactive_sub = 100 -  $percentage_active_sub;

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        $customerCounts = DB::table('customers')
            ->selectRaw('COUNT(*) AS total_customers, 
                         COUNT(CASE WHEN status = 0 AND ivr_status = 0 THEN 1 END) AS total_inactive,
                         COUNT(CASE WHEN DATE(created_at) = ? THEN 1 END) AS today_count, 
                         COUNT(CASE WHEN status IS NOT NULL THEN 1 END) AS total_sms, 
                         COUNT(CASE WHEN status = 1 THEN 1 END) AS charged_sms, 
                         COUNT(CASE WHEN status = -1 THEN 1 END) AS unsubscribed_sms, 
                         COUNT(CASE WHEN status IN (0, 1) THEN 1 END) AS active_sms, 
                         COUNT(CASE WHEN ivr_status IS NOT NULL THEN 1 END) AS total_ivr, 
                         COUNT(CASE WHEN ivr_status = 1 THEN 1 END) AS charged_ivr, 
                         COUNT(CASE WHEN ivr_status = -1 THEN 1 END) AS unsubscribed_ivr, 
                         COUNT(CASE WHEN ivr_status IN (0, 1) THEN 1 END) AS active_ivr,
                         COUNT(CASE WHEN DATE(created_at) = ? THEN 1 END) AS yesterday_count')
            ->setBindings([$today, $yesterday])
            ->first();
        
        $totalsmscustomer = $customerCounts->total_sms ?? 0;
        $chargedsmssubsriber = $customerCounts->charged_sms ?? 0;
        $unsubsmssubsriber = $customerCounts->unsubscribed_sms ?? 0;
        $activesmssubsriber = $customerCounts->active_sms ?? 0;

        $totalivrcustomer = $customerCounts->total_ivr ?? 0;
        $chargedivrsubsriber = $customerCounts->charged_ivr ?? 0;
        $unsubivrsubsriber = $customerCounts->unsubscribed_ivr ?? 0;
        $activeivrsubsriber = $customerCounts->active_ivr ?? 0;

        $customertoday = $customerCounts->today_count ?? 0;
        $customeryesterday = $customerCounts->yesterday_count ?? 0;

        if ($customeryesterday != 0) {
            $percentageChange = (($customertoday - $customeryesterday) / ($customeryesterday + $customertoday)) * 100;
        } else {
            $percentageChange = 0; // or any other value you want to assign when $customeryesterday is zero
        }
        $totalcustomers = $customerCounts->total_customers ?? 0;
        $totalainctive = $customerCounts->total_inactive ?? 0;


        return view('home', compact(
            'totalactive',
            'percentage_active_sub',
            'totalainctive',
            'percentage_inactive_sub',
            'recentmesages',
            'totalcustomers',
            'contentcreats',
            'customertoday',
            'customeryesterday',
            'totalsmscustomer',
            'chargedsmssubsriber',
            'unsubsmssubsriber',
            'activesmssubsriber',
            'totalivrcustomer',
            'chargedivrsubsriber',
            'unsubivrsubsriber',
            'activeivrsubsriber',
            'percentageChange'

        ));

    }



    public function getSmsChartData()
    {

        $smsSend = Log::selectRaw('count(id) as send,MONTH(created_at) as month')
            ->whereYear('created_at', date('Y'))
            ->groupby('month')
            ->get();

        $smsSend = $smsSend->reduce(function ($smsSend, $record) {
            $smsSend[$record->month] = $record->send;
            return $smsSend;
        });

        $monthWiseSms = [
            $smsSend[1] ?? 0,
            $smsSend[2] ?? 0,
            $smsSend[3] ?? 0,
            $smsSend[4] ?? 0,
            $smsSend[5] ?? 0,
            $smsSend[6] ?? 0,
            $smsSend[7] ?? 0,
            $smsSend[8] ?? 0,
            $smsSend[9] ?? 0,
            $smsSend[10] ?? 0,
            $smsSend[11] ?? 0,
            $smsSend[12] ?? 0
        ];
        return response()->json($monthWiseSms);
    }


    public function getSubscriptionData()
    {

        $smsSend =  Customer::selectRaw('count(id) as send,MONTH(created_at) as month')
            ->where('status', '=', 1)
            ->whereYear('created_at', date('Y'))
            ->groupby('month')
            ->get();

        $smsSend = $smsSend->reduce(function ($smsSend, $record) {
            $smsSend[$record->month] = $record->send;
            return $smsSend;
        });

        $monthWiseSms = [
            $smsSend[1] ?? 0,
            $smsSend[2] ?? 0,
            $smsSend[3] ?? 0,
            $smsSend[4] ?? 0,
            $smsSend[5] ?? 0,
            $smsSend[6] ?? 0,
            $smsSend[7] ?? 0,
            $smsSend[8] ?? 0,
            $smsSend[9] ?? 0,
            $smsSend[10] ?? 0,
            $smsSend[11] ?? 0,
            $smsSend[12] ?? 0
        ];


        //desubscription users
        $desubscribeuser =  Customer::selectRaw('count(id) as send,MONTH(created_at) as month')
            ->where('status', '=', 0)
            ->whereYear('created_at', date('Y'))
            ->groupby('month')
            ->get();

        $desubscribeuser = $desubscribeuser->reduce(function ($desubscribeuser, $record) {
            $desubscribeuser[$record->month] = $record->send;
            return $desubscribeuser;
        });

        $desubscribeusers = [
            $desubscribeuser[1] ?? 0,
            $desubscribeuser[2] ?? 0,
            $desubscribeuser[3] ?? 0,
            $desubscribeuser[4] ?? 0,
            $desubscribeuser[5] ?? 0,
            $desubscribeuser[6] ?? 0,
            $desubscribeuser[7] ?? 0,
            $desubscribeuser[8] ?? 0,
            $desubscribeuser[9] ?? 0,
            $desubscribeuser[10] ?? 0,
            $desubscribeuser[11] ?? 0,
            $desubscribeuser[12] ?? 0
        ];




        return response()->json(['subscription' => $monthWiseSms, 'desubscribeusers' => $desubscribeusers]);
    }


    //get the daily transaction
    private function getDailyTransaction()
    {
        $sales = DB::table('transactions')->select(DB::raw('id', 'amount_IN'), DB::raw('sum(amount_IN) as totalAmount'))
            ->whereDate('created_at', Carbon::today())
            ->where('status', 1)
            ->groupby('id')
            ->get();
        $dailytransactions = $sales->sum('totalAmount');
        return $dailytransactions;
    }


        //get the yesterday transaction
        private function getYesterdayTransaction()
        {
            $sales = DB::table('transactions')->select(DB::raw('id', 'amount_IN'), DB::raw('sum(amount_IN) as totalAmount'))
                ->whereDate('created_at', Carbon::yesterday())
                ->where('status', 1)
                ->groupby('id')
                ->get();
            $dailytransactions = $sales->sum('totalAmount');
            return $dailytransactions;
        }

    //get the daily transaction
    private function getWeeklyTransaction()
    {

        $sales = DB::table('transactions')->select(DB::raw('id', 'amount_IN'), DB::raw('sum(amount_IN) as totalAmount'))
            ->whereBetween('created_at',  [Carbon::now()->subDays(7), Carbon::now()])
            ->where('status', 1)
            ->groupby('id')
            ->get();

        $weekly = $sales->sum('totalAmount');

        return $weekly;
    }


    //get the daily transaction
    private function getMontlyTransaction()
    {

        $monthly = Log::whereMonth('created_at',  Carbon::today()->month)->count();

        return $monthly * 150;
    }

    //get the daily transaction
    private function getYearlyTransaction()
    {

        $yearly = Log::whereYear('created_at',  Carbon::today()->year)->count();

        return $yearly * 150;
    }
}

