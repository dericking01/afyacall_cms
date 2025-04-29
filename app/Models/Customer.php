<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'msisdn', 'status','ivr_status','doctor_subscription_status','keyword','enticement', 'ivr_enticement', 'doctor_enticement'
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }

    public function opts()
    {
        return $this->hasMany(Opt::class, 'customer_ID', 'id');
    }

    public function ivrstatistics()
    {
        return $this->hasMany(IvrStatistic::class, 'customer_id', 'id');
    }

    public function doctorstatistics()
    {
        return $this->hasMany(DoctorStastic::class, 'customer_id', 'id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'customer_id', 'id');
    }

    public function notifies()
    {
        return $this->hasMany(NotifySms::class, 'contactid', 'id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'customer_ID', 'id');
    }

    public static function updateCustomerHistory($customer_id, $status, $product_id)
    {

        $opt = new Opt();
        $opt->customer_ID = $customer_id;
        $opt->product_ID = $product_id;
        $opt->opt_value = $status;
        $opt->date = Opt::getServertime();
        $opt->save();

        if ($status != 1) {
            $subscrb = Subscription::where('customer_ID', $customer_id)->where('product_id', $product_id)->get()->first();
            if ($subscrb) {
                $subscrb->delete();
            }
            return true;
        } else {
            // add the customer to the susbscrptiion list
            $subscrb = Subscription::where('customer_ID', $customer_id)->where('product_id', $product_id)->get()->first();
            if ($subscrb) {
                $subscrb->customer_ID = $customer_id;
                $subscrb->product_id = $product_id;
                $subscrb->starts_at = Carbon::now();
                $subscrb->ends_at = Carbon::now()->addDays(1);
                $subscrb->save();
                return true;
            } else {
                $subscribe = new Subscription();
                $subscribe->customer_ID = $customer_id;
                $subscribe->product_id = $product_id;
                $subscribe->starts_at = Carbon::now();
                $subscribe->ends_at = Carbon::now()->addDays(1);
                $subscribe->save();
                return true;
            }
        }
    }
}
