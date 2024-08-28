<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\WebsiteEnticement;
use App\Models\Enticement;
use App\Models\Blacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Opt;
use App\Models\Customer;
use App\Models\Product;

class WebsiteApiController extends Controller
{
    public function websiteEnticement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'msisdn' => ['required'],
            'amount' => ['required'],
            'product' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $product = Product::where('product_ID', $request->product)->first();

        if (!$product) {
            return $this->responseJson('failed', 'Product not found', $request->product);
        }

        switch ($product->name) {
            case 'SMS':
                return $this->handleSmsProduct($request, $product);

            case 'IVR':
                return $this->handleIvrProduct($request, $product);

            case 'Doctor Subscriptions':
                return $this->handleDoctorProduct($request, $product);

            default:
                return $this->responseJson('99', 'Product is not defined or is on the blacklist', $request->msisdn);
        }
    }

    private function handleSmsProduct(Request $request, $product)
    {
        $customer = Customer::where('msisdn', $request->msisdn)->first();

        if ($customer) {
            if ($customer->enticement == 0) {
                return $this->processEnticement($request->msisdn, $product->product_ID, $product->id);
            }
            return $this->responseJson('failed', 'Already subscribed to this service '.$product->name, $request->msisdn);
        }

        $customer = $this->createNewCustomer($request->msisdn, 'Website', 'enticement');
        $this->createOptRecord($customer->id, $product->id);

        return $this->processEnticement($request->msisdn, $product->product_ID, $product->id);
    }

    private function handleIvrProduct(Request $request, $product)
    {
        $customer = Customer::where('msisdn', $request->msisdn)->first();

        if ($customer) {
            if ($customer->ivr_enticement == 0) {
                return $this->processEnticement($request->msisdn, $product->product_ID, $product->id);
            }
            return $this->responseJson('failed', 'Already subscribed to this service '.$product->name, $request->msisdn);
        }

        $customer = $this->createNewCustomer($request->msisdn, 'Website', 'ivr_status');
        $this->createOptRecord($customer->id, $product->id);

        return $this->processEnticement($request->msisdn, $product->product_ID, $product->id);
    }

    private function handleDoctorProduct(Request $request, $product)
    {
      
        $customer = Customer::where('msisdn', $request->msisdn)->first();

        if ($customer) {
            if ($customer->doctor_enticement == 0) {
                return $this->processEnticement($request->msisdn, $product->product_ID, $product->id);
            }
            return $this->responseJson('failed', 'Already subscribed to this service '.$product->name, $request->msisdn);
        }

        $customer = $this->createNewCustomer($request->msisdn, 'Website', 'doctor_status');
        $this->createOptRecord($customer->id, $product->id);

        return $this->processEnticement($request->msisdn, $product->product_ID, $product->id);
    }

    private function createNewCustomer($msisdn, $source, $statusField)
    {
        $customer = new Customer();
        $customer->msisdn = $msisdn;
        $customer->registered_at = Opt::getServertime();
        $customer->$statusField = 0;
        $customer->source = $source;
        $customer->save();

        return $customer;
    }

    private function createOptRecord($customerId, $productId)
    {
        $opt = new Opt();
        $opt->customer_ID = $customerId;
        $opt->product_ID = $productId;
        $opt->opt_value = 1;
        $opt->date = Opt::getServertime();
        $opt->save();
    }

    private function processEnticement($msisdn, $productId,$id)
    {
        $data = Enticement::pushEnticement($msisdn, $productId,$id);

        if ($data['output_ResponseCode'] == 0) {
            return $this->responseJson('success', $data['output_ResponseDesc'], $msisdn);
        }

        return $this->responseJson('failed', $data['output_ResponseDesc'], $msisdn);
    }

    private function responseJson($status, $message, $msisdn)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'msisdn' => $msisdn,
        ]);
    }
}
