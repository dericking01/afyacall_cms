<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outboundcall extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function deliveries()
    {
        return $this->hasMany(SmsDelivery::class,'obdid','id');
    }

}

