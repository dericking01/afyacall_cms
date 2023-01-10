<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TicketMail;
use App\Models\Product;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = Ticket::with('product')->get();
        $totalticket = Ticket::count();
        $totalopenticket = Ticket::where('status',0)->count();
        $totalprogressticket = Ticket::where('status',1)->count();
        $totalclosedticket = Ticket::where('status',-1)->count();
        $products = Product::all()->pluck('name','id');
        return view('admin.tickets.index',compact('products','tickets','totalticket','totalopenticket','totalprogressticket','totalclosedticket'))->with('no', 1);
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
        $ticket = new Ticket();
        $ticket->msisdn = $request->msisdn;
        $ticket->message = $request->message;
        $ticket->user_id = Auth::id();
        $ticket->reference = 'ATC-'.Ticket::getReferenceNumber();
        $ticket->product_id = $request->product_id;
        $ticket->save();

        try {
             $details = [
            'number' => $request->msisdn,
            'message' =>  $request->message,
           ];

        //  email to receive tickets
          Mail::to('julius.john@it.co.tz')
              ->send(new TicketMail($details));
              
        //send notification to normal sms
        // $this->sendnotificationticket('255746805383',$request->message);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
      


        return redirect()->route('admin.ticket.index')->with('success', 'Ticket Saved Successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        if ($ticket) {
            $ticket->status = 1;
            $ticket->save();
        }
        return redirect()->route('admin.ticket.index')->with('success', 'Ticket Saved Successful!');
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
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function close(Request $request)
    {
            $ticket = Ticket::findOrFail($request->ticket_id);
            if ($ticket) {
                $ticket->status = -1;
                $ticket->solution = $request->resolution;
                $ticket->save();
            }

        return redirect()->route('admin.ticket.index')->with('close', 'Ticket Saved Successful!');
    }

    public function open($id)
    {
            $ticket = Ticket::findOrFail($id);
            if ($ticket) {
                $ticket->status = 0;
                $ticket->save();
            }

        return redirect()->route('admin.ticket.index')->with('open', 'Ticket Saved Successful!');
    }

    
    public function sendnotificationticket($msisdn,$message)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $client->request('GET', 'http://192.168.1.10:6013/cgi-bin/sendsms', [
                'query' => [
                    'username' => 'afya',
                    'password' => 'Afya4017',
                    'from' => '15723',
                    'to' => $msisdn,
                    'text' => $message,
                ]
            ]);
        } catch (\Throwable $th) {

        }
    }
}

