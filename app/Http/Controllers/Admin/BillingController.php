<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {

        $billing = Billing::latest()->first();

        return view('admin.billing.index', compact('billing'));
    }

    public function update(Request $request)
    {

        $billing = new Billing;
        $billing->company = $request->company;
        $billing->address_one = $request->address_one;
        $billing->register_number = $request->register_number;
        $billing->address_two = $request->address_two;
        $billing->country = $request->country;
        $billing->state = $request->state;
        $billing->vatnumber = $request->vatnumber;
        $billing->invoice_email = $request->invoice_email;
        $billing->zipcode = $request->zipcode;
        $billing->vatregister = $request->vatregister;
        $billing->save();

        return redirect()->route('admin.billing')->with('success', 'update billing');
    }
}
