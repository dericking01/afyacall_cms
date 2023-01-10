<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;


    public static function getReferenceNumber()
    {
        $lastNumber = Invoice::orderBy('created_at', 'desc')->get()->first();
        if (!$lastNumber) {
            $number = 0;
        } else {
            $number =explode("-",$lastNumber->invoice_reference);
            $number = $number[1];
        }
        return sprintf('%06d', intval($number) + 1);
    }
}
