<?php

namespace App\Services\Orders;

use App\Models\Customer;
class CreateCustomerService
{
    public function handle(array $data): Customer
    {
        $customer = Customer::query()->where('email', $data['email'])->first();

        if ($customer) {
            return $customer;
        }
        $customer = Customer::query()->create($data);
        
        return $customer;
    }
}
