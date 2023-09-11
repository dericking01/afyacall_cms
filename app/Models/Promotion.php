<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{

    protected $fillable = [
        'name', 'msisdn','amount','status','product_id'
    ];
    use HasFactory;
}
