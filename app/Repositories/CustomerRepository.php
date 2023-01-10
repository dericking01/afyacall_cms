<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Opt;

class CustomerRepository
{

    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }


    public function getAll()
    {
        return $this->customer
            ->get();
    }

    
    public function checkifexists($msisdn){

        return $this->customer
                ->where('msisdn', $msisdn)
                ->exists();
    }

    
    public function savenewcontact($data)
    {
        $customer = new $this->customer;
        $customer->msisdn = $data['msisdn'];
        $customer->registered_at = Opt::getServertime();
        $customer->source = 'ICG';
        $customer->save();
     
        return $customer->fresh();
    }

}