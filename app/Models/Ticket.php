<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    public static function getReferenceNumber()
    {
        $lastNumber = Ticket::orderBy('created_at', 'desc')->first();
        if (!$lastNumber) {
            $number = 0;
        } else {
            $number =explode("-",$lastNumber->reference);
            $number = $number[1];
        }
        return sprintf('%06d', intval($number) + 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }


    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
