<?php

namespace App\Http\Controllers\Admin;
use App\Models\MonthRevenue;
use App\Http\Controllers\Controller;
use App\Models\ContentType;
use App\Models\Customer;
use App\Models\Log;
use App\Models\ViewRevenue;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sms()
    {
        return view('admin.reports.general.sms');
    }

    public function ivr()
    {
        return view('admin.reports.general.ivr');
    }

    public function calls()
    {
        return view('admin.reports.general.calls');
    }

    public function daterangeforivr(Request $request)
    {

        $startdatetime =  $request->startDate;
        $enddatetime =  $request->endDate . ' 23:59:59';
        $status = $request->status;

        if ($status == "all") {
            $posts = Customer::whereBetween('created_at', [$startdatetime, $enddatetime])
                ->get();
        } else {
            $posts = Customer::where('status_ivr', $status)
                ->whereBetween('created_at', [$startdatetime, $enddatetime])
                ->get();
        }

        return response()->json(["data" => $posts], 200);
    }

    public function daterange(Request $request)
    {
        $startdatetime =  $request->startDate;
        $enddatetime =  $request->endDate . ' 23:59:59';
        $status = $request->status;

        if ($status == "all") {
            $posts = Customer::whereBetween('created_at', [$startdatetime, $enddatetime])
                ->get();
        } else {
            $posts = Customer::where('status', $status)
                ->whereBetween('created_at', [$startdatetime, $enddatetime])
                ->get();
        }

        return response()->json(["data" => $posts], 200);
    }

    public function numbersearch()
    {

        return view('admin.reports.numbersearch');
    }

    public function findnumber(Request $request)
    {

        $startdatetime =  $request->startDate;
        $enddatetime =  $request->endDate . ' 23:59:59';
        $status = $request->status;

        $results = Customer::with(['opts' => function ($t) {
            return $t->with('product');
        }])
            ->with(['ivrstatistics', 'transactions'])
            ->withCount(['ivrstatistics', 'logs', 'transactions', 'opts'])
            ->with(['logs' => function ($q) {
                return $q->with('content');
            }])
            ->where('msisdn', $request->msisdn)
            ->whereBetween('created_at', [$startdatetime, $enddatetime])
            ->first();

        return response()->json(["data" => $results], 200);
    }

    public function contentview()
    {

        return view('admin.reports.content');
    }

    //revenue reports views
    public function revenueview()
    {

        return view('admin.reports.revenue');
    }

    public function revenueSearch(Request $request)
    {

        $startdatetime =  $request->startDate;
        $enddatetime =  $request->endDate . ' 23:59:59';

 $results = ViewRevenue::whereBetween('DateCreated', [$startdatetime, $enddatetime])->get();
       

        return response()->json(["data" => $results], 200);
    }

    //message reports
    public function messagereportview()
    {

        $contenttypes = ContentType::all()->pluck('name', 'id');
        return view('admin.reports.message', compact('contenttypes'));
    }

    public function messagereports(Request $request)
    {
        $startdatetime =  $request->startDate;
        $enddatetime =  $request->endDate . ' 23:59:59';
        $status = $request->status;

        if ($request->status == "all") {
            $results = Log::with(['content' => function ($q) {
                return $q->with('type');
            }])->with('customer')->whereBetween('created_at', [$startdatetime, $enddatetime])->get();
        } else {
            $results = Log::with(['content' => function ($q) use ($status) {
                return $q->with('type')->where('content_type', $status);
            }])->with('customer')->whereBetween('created_at', [$startdatetime, $enddatetime])->get();
        }

        return response()->json(["data" => $results], 200);
    }

        //monthly revenue reports
    public function monthlyrevenueReport()
    {

        $monthlys = MonthRevenue::all();
        return view('admin.reports.monthly_revenue',compact('monthlys'));
    }
}
