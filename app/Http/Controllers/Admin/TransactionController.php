<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\Opt;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::with('customer')->with('product')->orderBy('created_at', 'desc')->paginate(1000);
        return view('admin.transaction.index', compact('transactions'))->with('no', 1);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }


    public function mpesa_analytics()
    {
        return view('admin.transaction.mpesa_analysis');

    }
    public function doctortranscations()
    {
        $transactions = Transaction::where('status', 1)->where('product_id', 3)->with('customer')->with('product')->orderBy('created_at', 'desc')->paginate(1000);
        return view('admin.transaction.doctortransaction', compact('transactions'))->with('no', 1);
    }

    public function get_mpesa_analytics()
    {

        $smsSend = Transaction::selectRaw('count(id) as send,MONTH(created_at) as month')
        ->whereYear('created_at', date('Y'))
        ->where('status', 1)
        ->where('currency', 'Mpesa')
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
            $desubscribeuser =  Transaction::selectRaw('count(id) as send,MONTH(created_at) as month')
            ->where('status', 0)
            ->where('currency', 'Mpesa')
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

    return response()->json(['success' => $monthWiseSms, 'fail' => $desubscribeusers]);
 
    }


}
