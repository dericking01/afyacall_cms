<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
class RatingController extends Controller
{
    public function rating(Request $request){

	       Log::info($request);
        $validator = Validator::make(
            $request->all(),
            [
                'rating' => ['required'],
            ]
        );

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }
        $customer = Customer::where('msisdn', $request->msisdn)->get()->first();
        $product = Product::where('product_ID', '921465_P02')->get()->first();
        if ($customer) {
            # code...
            $rating = new Rating();
            $rating->customer_id = $customer->id;
            $rating->product_id = $product->id;
            $rating->rating = $request->rating;
            $rating->comment = $request->comment;
            $rating->status = 1;
            $rating->save();
        }
        return true;
    }
}
