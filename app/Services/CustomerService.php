<?php

namespace App\Services;

use App\Repositories\CustomerRepository;

class CustomerService
{

    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getAll()
    {
        return $this->customerRepository->getAll();
    }

    public function subscribe_sms($data)
    {
        return $this->customerRepository->subscribe_sms($data);
    }
    public function unsubscribe_sms($data){

        return $this->customerRepository->unsubscribe_sms($data);
    }
    public function subscribe_ivr($data)
    {
        return $this->customerRepository->subscribe_ivr($data);
    }

    public function unsubscribe_ivr($data){

        return $this->customerRepository->unsubscribe_ivr($data);
    }

    public function subscribe_doctor_sub($data)
    {
        return $this->customerRepository->subscribe_doctor_sub($data);
    }

    public function unsubscribe_doctor_subscription($data){
        return $this->customerRepository->unsubscribe_doctor_subscription($data);
    }

    public function unsubscribe_allservices($data){
        return $this->customerRepository->unsubscribe_allservices($data);
    }
}
