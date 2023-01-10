<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public function activate()
    {
        $this->update(['is_active' => true]);

        return $this;
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);

        return $this;
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id','customer_ID');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id','product_id');
    }
}
