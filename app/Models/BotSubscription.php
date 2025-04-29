<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class BotSubscription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'msisdn',
        'content',
        'starts_at',
        'ends_at',
    ];

    protected $dates = [
        'starts_at',
        'ends_at',
        'deleted_at',
    ];
}
