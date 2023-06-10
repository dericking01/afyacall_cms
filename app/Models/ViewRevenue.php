<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewRevenue extends Model
{
    use HasFactory;

    public $table = "today_revenue_transaction_view";
}
