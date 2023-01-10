<?php

namespace App\Http\Controllers\Admin;
use App\Http\Helpers\ChargingExistingCustomer;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Jobs\ProcessMpesaDaily;
use Illuminate\Support\Facades\Log;
use App\Models\Deliveryobd;
use App\Models\Group;
use App\Models\Opt;
use App\Jobs\DailyJobSms;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessCharingDaily;
class SubscriptionController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


	           //$subscriptions = Subscription::with('customer')->get();
        $subscriptions = DB::table('subscriptions')
						 ->join('customers', 'customers.id', '=', 'subscriptions.customer_ID')
						 ->join('products', 'products.id', '=', 'subscriptions.product_id')
						 ->get();
		return view('admin.subscription.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    
    {


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        //
    }
}
