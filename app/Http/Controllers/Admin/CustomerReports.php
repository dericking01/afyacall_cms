<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blacklist;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerReports extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('admin.reports.customers.customer_list', compact('customers'));
    }

    public function customerList(Request $request)
    {

        $startdatetime =  $request->startDate;
        $enddatetime =  $request->endDate . ' 23:59:59';
        // $status = $request->status;


        $posts = Customer::whereBetween('created_at', [$startdatetime, $enddatetime])->get();


        return response()->json(["data" => $posts], 200);
    }

    public function blacklist()
    {
        $users = User::all();
        return view('admin.reports.customers.blacklist', compact('users'));
    }

    public function blacklistSearch(Request $request)
    {

        $startdatetime =  $request->startDate;
        $enddatetime =  $request->endDate . ' 23:59:59';

        if ($request->user == "all") {
            $results = Blacklist::with('user')->whereBetween('created_at', [$startdatetime, $enddatetime])->get();
        } else {
            $results = Blacklist::where('user_id', $request->user)
                ->with('user')
                ->whereBetween('created_at', [$startdatetime, $enddatetime])->get();
        }
        return response()->json(["data" => $results], 200);
    }

    public function ticketreport()
    {
        $users = User::all();
        return view('admin.reports.customers.ticketreport', compact('users'));
    }

    public function ticketingSearch(Request $request)
    {

        $startdatetime =  $request->startDate;
        $enddatetime =  $request->endDate . ' 23:59:59';

        if ($request->status == "all") {
            $results = Ticket::with('user')->whereBetween('created_at', [$startdatetime, $enddatetime])->get();
        } else {
            $results = Ticket::where('status', $request->status)
                ->with('user')
                ->whereBetween('created_at', [$startdatetime, $enddatetime])->get();
        }

        return response()->json(["data" => $results], 200);
    }
}
