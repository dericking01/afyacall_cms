<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\ProcessLanguage;
use App\Models\Blacklist;
use App\Models\Customer;
use App\Models\Opt;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\ViewRevenue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        $customers = Customer::with('products')->with('opts')->orderBy('created_at', 'desc')->paginate(1000);
        // return response()->json($customers);
        return view('admin.customers.index', compact('customers'));
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
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {

        // return response()->json($customer);
        $customers = Customer::where('id', $customer->id)->first();
	    // $customers = Customer::with(['opts' => function ($t) {
        //     return $t->with('product');
        // }])
        //     ->with(['ivrstatistics', 'transactions', 'doctorstatistics', 'notifies'])
        //     ->withCount(['ivrstatistics', 'logs', 'transactions', 'opts', 'doctorstatistics'])
        //     ->with(['logs' => function ($q) {
        //         return $q->with('content');
        //     }])
        //     ->orderBy('created_at', 'desc')
        //     ->where('id', $customer->id)
        //     ->first();
        // return response()->json($customers);
        return view('admin.customers.view', compact('customers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }

    public function subscribecustomer(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        $product = Product::where('product_ID', '921465_P02')->get()->first();
        if ($customer) {
		$customer->status = -1;
		$customer->ivr_status = -1;
                $customer->save();

                //check if exist in subscription

                $subscribeid = Subscription::where('customer_ID', $customer->id)->first();
                if ($subscribeid) {
                    $subscribeid->delete();
                }


                //update the opt in 
                $opt = new Opt();
                $opt->customer_ID = $customer->id;
                $opt->ConversationID = 'From System';
                $opt->OriginatorConversationID = $request->category ?? '';
                $opt->opt_value = 0;
                $opt->date = Opt::getServertime();
                $opt->save();

                //send notification to a customer
                $sw = 'Umefanikiwa kujitoa kikamilifu kwenye huduma ya AFYACALL .Kujiunga tena na huduma hii tuma neno AFYASMS kwenda 15723 kwa gharama ya Tsh 150/siku.';
                $en = 'You have successfully unsubscribed from AFYACALL  service. To rejoin this service send the word AFYASMS to 15723 at a cost of Tzs 150/day';
		        ProcessLanguage::dispatchSync($customer->msisdn, $sw, $en);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Successful!');
    }

    public function checkbalance(Request $request)
    {

        $payload = [
            'serviceIdentifier'   =>
            array(
                'value' => $request->customer_number,
                'schemeName' => 'msisdn'
            ),
            'id' => [
                array(
                    'value' => "airtime:*199*100#",
                    'schemeName' => "balanceType"
                )
            ]
        ];

        //time for charging
        $chargetime = Opt::getServertime();
        try {
            $client = new \GuzzleHttp\Client;
            $credentials = base64_encode('svc_afyacall:wHroRA3U03_el701');
            $response = $client->post('https://197.250.9.149:6202/middlewarev2/serviceBalance', [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => ' application/json',
                    'X-MessageId' => 'uuid: a5c49974-353e-11e5-a151-feff819cdc9f',
                    'X-Source-Timestamp'  => $chargetime,
                ],
                'json' => $payload
            ]);
            $balances = $response->getBody()->getContents();

            return $balances;
        } catch (\Throwable $th) {
            return response()->json(["data error" => $th->getMessage()]);
        }
    }


    public function search(Request $request)
    {
        $search =  $request->input('q');
        if ($search != "") {
            $customers = Customer::where(function ($query) use ($search) {
                $query->where('msisdn', 'like', '%' . $search . '%');
            })
                ->paginate(100);
            $customers->appends(['q' => $search]);
        } else {
            $customers = Customer::paginate(100);
        }
        return view('admin.customers.index', compact('customers'))->with('no', 1);
    }
}
