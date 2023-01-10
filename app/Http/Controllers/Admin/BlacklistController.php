<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\BlacklistImport;
use App\Models\Blacklist;
use App\Models\Customer;
use App\Models\Opt;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class BlacklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
 $blacklists = Blacklist::with('user')->orderBy('created_at', 'desc')->paginate(100);
        return view('admin.blacklists.index', compact('blacklists'))->with('no', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer = Customer::where('msisdn', $request->msisdn)->get()->first();

        if ($customer) {
            //add the customer to the blacklist
            $blacklist = new Blacklist();
            $blacklist->msisdn = $request->msisdn;
            $blacklist->reason = $request->reason;
            $blacklist->user_id = Auth::id();
            $blacklist->save();

            //update the opt in 
            $opt = new Opt();
            $opt->customer_ID = $customer->id;
            $opt->ConversationID = 'Added to Blacklist';
            $opt->opt_value = 0;
            $opt->date = Opt::getServertime();
            $opt->save();

            //check if exist in subscription
            $subscribeid = Subscription::where('customer_ID', $customer->id)->first();
            if ($subscribeid) {
                $subscribeid->delete();
            }

            $customer->delete();
        } else {

            $blacklist = new Blacklist();
            $blacklist->msisdn = $request->msisdn;
            $blacklist->reason = $request->reason;
            $blacklist->user_id = Auth::id();
            $blacklist->save();
        }

        return redirect()->route('admin.blacklist.index')->with('success', 'Successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $blacklist = Blacklist::find($id);
        //create customer before delete in blacklist
        $excustomer = Customer::where('msisdn', $blacklist->msisdn)->get()->first();
        if (!$excustomer) {
            //restore customer from softdelete
            $customer_restore = Customer::withTrashed()->where('msisdn', $blacklist->msisdn)->restore();
            if ($customer_restore) {
                $excustomerrestore = Customer::where('msisdn', $blacklist->msisdn)->get()->first();
                if ($excustomerrestore) {
                    $excustomerrestore->status = 0;
                    $excustomerrestore->updated_at = Carbon::now();
                    $excustomerrestore->save();

                    //update the opt in 
                    $opt = new Opt();
                    $opt->customer_ID = $excustomerrestore->id;
                    $opt->ConversationID = 'Removed From Blacklist';
                    $opt->opt_value = 1;
                    $opt->date = Opt::getServertime();
                    $opt->save();
                }
            }
        }
        //delete from blacklist
        $blacklist->delete();

        return redirect()->route('admin.blacklist.index')->with('success', 'sms content delete Successful!');
    }

    public function blacklistcustomer(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        if ($customer) {

            //add the customer to the blacklist
            $blacklist = new Blacklist();
            $blacklist->msisdn = $customer->msisdn;
            $blacklist->reason = $request->reason;
            $blacklist->user_id = Auth::id();
            $blacklist->save();

            //update the opt in 
            $opt = new Opt();
            $opt->customer_ID = $customer->id;
            $opt->ConversationID = 'Added to Blacklist';
            $opt->opt_value = 0;
            $opt->date = Opt::getServertime();
            $opt->save();

            //check if exist in subscription
            $subscribeid = Subscription::where('customer_ID', $customer->id)->first();
            if ($subscribeid) {
                $subscribeid->delete();
            }

            $customer->delete();
        }
        return redirect()->route('admin.customers.index')->with('success', 'Successful!');
    }

    public function import()
    {
        Excel::import(new BlacklistImport, request()->file('file'));
        Log::info('contacts uploaded successfully by ' . Auth::user()->name);
        return back();
    }

    public function search(Request $request)
    {
        $search =  $request->input('q');
        if($search!=""){
            $blacklists = Blacklist::where(function ($query) use ($search){
                $query->where('msisdn', 'like', '%'.$search.'%')
                    ->orWhere('reason', 'like', '%'.$search.'%');
            })
            ->paginate(100);
            $blacklists->appends(['q' => $search]);
        }
        else{
            $blacklists = Blacklist::paginate(100);
        }
        return view('admin.blacklists.index', compact('blacklists'))->with('no', 1);
    }




}
