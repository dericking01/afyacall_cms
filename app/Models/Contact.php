<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = [
        'msisdn',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class,'campaign_id');
    }
}
