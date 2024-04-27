<?php

namespace App\Http\Controllers\Admin;
use App\Models\SmsDelivery;
use App\Http\Controllers\Controller;
use App\Imports\CampaignImport;
use App\Jobs\BroadcastCampaign;
use App\Jobs\ProcessContactFilterDND;
use App\Models\Blacklist;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Group;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{

    public function index()
    {
        $campaigns = Campaign::all();
        return view('admin.contacts.index', compact('campaigns'))->with('no', 1);
    }

    public function create()
    {
        $groups  = Group::with('user')->withCount('contact')->get();
        return view('admin.contacts.create', compact('groups'));
    }


    public function showcontact($id)
    {
        try {
            $group = Group::findOrFail($id);
            $contacts = Contact::where('campaign_id', $id)
                ->with('campaign') // Assuming there is a relationship defined
                ->paginate(100);
            $contactCounts = Contact::where('campaign_id', $id)->count();
            
            return view('admin.contacts.contactview', compact('contacts', 'id', 'contactCounts', 'group'))
                ->with('no', 1);
        } catch (\Exception $e) {
            // Handle the exception, display an error message, or redirect to an error page
            return response()->view('error', ['message' => 'An error occurred.']);
        }
    }
    
    // public function showcontact($id)
    // {
	//     $group = Group::whereId($id)->first();
    //     // return $post;
    //     $contacts = Contact::with('campaign')->where('campaign_id', $id)->paginate(100);
    //     $contactCounts = Contact::where('campaign_id', $id)->count();
    //     return view('admin.contacts.contactview', compact('contacts', 'id', 'contactCounts','group'))->with('no', 1);
    // }

    public function fileUpload(Request $req)
    {
        $req->validate([
            'file' => 'required|mimes:csv|max:2048'
        ]);

        $fileModel = new Contact();

        if ($req->file()) {
            $fileName = time() . '_' . $req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');

            $fileModel->name = time() . '_' . $req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/' . $filePath;
            $fileModel->save();

            return back()
                ->with('success', 'File has been uploaded.')
                ->with('file', $fileName);
        }
    }

    public function compaingservice()
    {
        $todaydate = Carbon::now()->toDateTimeString();
        $groups  = Group::all();
        return view('admin.contacts.compaigns', compact(['todaydate', 'groups']));
    }

    public function groups()
    {
	      $groups  = Group::all();
        return view('admin.contacts.groups.index', compact('groups'))->with('no', 1);
    }


    public function groups_store(Request $request)
    {
        $group = new Group();
        $group->name = $request->groupname;
        $group->Description = $request->description;
        $group->expire = Carbon::now()->addDays($request->retention);
        $group->created_by = Auth::id();
        $group->save();

        return redirect()->route('admin.contact.groups')->with('success', 'Group Created Successful!');
    }

    public function groupImport($id)
    {
        $group = Group::find($id);
        return view('admin.contacts.groups.import', compact('group'));
    }

    public function importContactGroup(Request $request)
    {

	       $blacklistArray = array();
        if ($request->has('inputupload')) {


            //save a file first
            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads/campaign', $fileName, 'public');

            if ($request->file()) {
                $datafiles = file($request->file('file'));
		// Chunking file
	
	        Blacklist::query()->select('msisdn')->chunk(10000, function ($blacklists)
                use (&$blacklistArray) {
        
                    foreach ($blacklists as $blacklist) {
        
                        $blacklistArray[] = $blacklist->msisdn;
                    }
		});



                //check with customer who opt out


                $chunks = array_chunk($datafiles, 1000);

		 $batch  = Bus::batch([])->onQueue('upload')->dispatch();

                foreach ($chunks as $key => $chunk) {
                    $data = array_map('str_getcsv', $chunk);

                    if ($key === 0) {
                        unset($data[0]);
		    }

		      $batch->add(new ProcessContactFilterDND($data, $blacklistArray,$request->group_id));

                }
                return redirect()->route('admin.contact.groups')->with('success', 'Saved Successful!');
            }
        } else {
            $receipts = explode(',', $request->receipts);
            if ($request->removedout) {
                //check if contact exist in customer database with removed out status
                foreach ($receipts as $msisdn) {
                    $blacklisted = Blacklist::where('msisdn', $msisdn)->get()->first();
                    if (!$blacklisted) {
                        $contact = new Contact();
                        $contact->msisdn = $msisdn;
                        $contact->campaign_id = $request->group_id;
                        $contact->save();
                    } else {
                        # Generate report

                    }
                };
            }
        }
        return redirect()->route('admin.contact.groups')->with('success', 'Saved Successful!');
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

    public function pushcompaignservice(Request $request)
    {
        $count = 0;
        $importData_arr = array();
        $groupnames = array();

        $batch  = Bus::batch([])->dispatch();

        $campaing = new Campaign();
        $campaing->name = $request->compaignname;
        $campaing->message = $request->message;
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
        $campaing->delivery = $count;
        $campaing->uploadvia = implode(",", $groupnames);
	   if ($campaing->save()) {

            $contactsbatches = array_chunk($importData_arr, 10);
            Log::info('number of batch to products => ' . count($contactsbatches));
            foreach ($contactsbatches as $key => $contactsbatche) {
                $receipts = implode(",", $contactsbatche);
                $batch->add(new BroadcastCampaign($receipts, $campaing->id));
            }

            return redirect()->route('admin.contact.campaign')->with('success', 'Saved Successful!');
        }
    }

    public function campaigndetails($id)
    {
	            $campaignsdata = SmsDelivery::where('campaignid', $id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $status = $campaignsdata->reduce(function ($status, $record) {
            $status[$record->status] = $record->total;
            return $status;
        });

        $deliveries = [
            [
                'status' =>  'Delivery to Kannel',
                'total' => $status[null] ?? 0
            ],
            [
                'status' =>  'Delivery to Vodacom SMSC',
                'total' => $status[8] ?? 0
            ],
            [
                'status' =>  'Delivery to Customer',
                'total' => $status[1] ?? 0
            ],
            [
                'status' =>  'Non Delivery to Customer',
                'total' => $status[2] ?? 0
            ],
            [
                'status' =>  'Queued at Vodacom',
                'total' => $status[4] ?? 0
            ],
            [
                'status' =>  'Non Delivery to Vodacom SMSC',
                'total' => $status[16] ?? 0
            ],
        ];


        $deliveries = json_decode(json_encode($deliveries));

        return view('admin.contacts.campaignstatics', compact('deliveries'));
    }

    public function destroyContact($id)
    {

        Contact::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Contact Deleted Successfully!!');
    }

    public function destroyGroup($id)
    {

        $group = Group::where('id', $id)->get()->first();



        if ($group) {
            Contact::where('campaign_id', $group->id)->chunk(5000, function ($contacts) {
                foreach ($contacts as $contact) {

                    $contact->delete();
                }
	    });

	      $group->delete();
        }


        return redirect()->back()->with('success', 'Group Deleted Successfully!!');
    }
}
