<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id','customer_ID');
    }

    public function product(){
 
        return $this->belongsTo(Product::class,'product_id');
        
    }
}
