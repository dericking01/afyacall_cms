<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Deliveryobd extends Model
{
    use HasFactory;

public function scopeCampaignStatusCount($query, $id)
{
    return $query->where('obdid', $id)
        ->select('status', DB::raw('COUNT(*) as total'))
        ->groupBy('status');
}
}
