<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessOBD;
use App\Models\Group;
use App\Models\Opt;
use App\Models\Deliveryobd;
use App\Models\Outboundcall;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutBoundCampaign extends Controller
{

    public function index()
    {
        $campaigns = Outboundcall::all();
        return view('admin.campaign.index', compact('campaigns'))->with('no', 1);
    }

    public function show($id)
    {
        $campaigns = Deliveryobd::where('obdid', $id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return view('admin.campaign.show', compact('campaigns'));
    }

    public function create()
    {

        $todaydate = Carbon::now()->toDateTimeString();
        $groups  = Group::all();
        return view('admin.campaign.create', compact(['todaydate', 'groups']));
    }
    public function batch()
    {
        $batchId = request('id');
        return Bus::findBatch($batchId);
    }

    public function batchInProgress()
    {
        $batches = DB::table('job_batches')->where('pending_jobs', '>', 0)->get();
        if (count($batches) > 0) {
            return Bus::findBatch($batches[0]->id);
        }

        return [];
    }
    public function store(Request $request)
    {
        $count = 0;
        $importData_arr = array();
        $groupnames = array();

        $batch  = Bus::batch([])->dispatch();

        $campaing = new Outboundcall();
        $campaing->obdname = $request->obdname;
        $campaing->maxretries = $request->maxretries;
        $campaing->retrytime = $request->retrytime;
        $campaing->waittime = $request->waittime;
        $campaing->created_by = Auth::id();
        $groups = Group::withCount('contact')->with('contact')->whereIn('id', $request->groups)->get();
        foreach ($groups as $key => $group) {
            $count =  $count + $group->contact_count;
            $groupnames[] = $group->name;
            foreach ($group->contact as $key => $contact) {
                $importData_arr[] = $contact->msisdn;
            }
            Log::info('import to array has been finished ' . count($importData_arr));
        }
        $campaing->other = $count;
        $campaing->uploadvia = implode(",", $groupnames);
        if ($campaing->save()) {
            $contactsbatches = array_chunk($importData_arr, 100);
            Log::info('number of batch to broadcast => ' . count($contactsbatches));
            foreach ($contactsbatches as $key => $contactsbatche) {
                $receipts = implode(",", $contactsbatche);
                $batch->add(new ProcessOBD($receipts, $campaing->id));
            }

            return redirect()->route('admin.contact.outboundcampaing')->with('success', 'Saved Successful!');
        }
    }
}

