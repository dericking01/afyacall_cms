<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\WebsiteEnticement;
use App\Models\Blacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Opt;

class WebsiteApiController extends Controller
{

    public function websiteEnticement(Request $request)
    {
    }
}
