<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ContactImport;
use App\Models\Enticement;
use App\Models\Opt;
use App\Services\EnticementService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EnticementController extends Controller
{

    protected $enticementService;

    public function __construct(EnticementService $enticementService)
    {
        $this->enticementService = $enticementService;
    }

    public function index()
    {
        try {
            $enticements = $this->enticementService->getAll();
        } catch (Exception $e) {
            $enticements = [
                'status' => 500,
            ];
        }

        return view('admin.enticements.index', compact('enticements'))->with('no', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $enticement = Enticement::find($id);
        $enticement->delete();
        return redirect()->route('admin.enticement.index')->with('success', 'Contacts delete Successful!');
    }


    public function import()
    {
        Excel::import(new ContactImport, request()->file('file'));
        Log::info('contacts uploaded successfully by ' . Auth::user()->name);
        return back();
    }


    public function enticeallcontacts(Request $request)
    {
        $ids = $request->ids;
        Enticement::where('status', 0)->whereIn('id', explode(",", $ids))
            ->chunkById(1000, function ($enticements) {
                foreach ($enticements as $enticement) {

                    $code = Opt::getCode();
                    try {
                        $client = new \GuzzleHttp\Client();
                        $response = $client->request('POST', 'http://197.250.9.128:23000/icg/Enticement/', [
                            'headers' => [
                                'Content-Type' => ' application/json',
                            ],
                            'json' => [
                                'input_Username' => '102047',
                                'input_Password' => 'AnF301LeGXrq-pI12',
                                'input_WASPShortcode' => '102047',
                                'input_ProductID' => '102047_P01',
                                'input_CustomerMSISDN' => $enticement->msisdn,
                                'input_OriginatorConversationID' => $code,
                                'input_EnticementChannel' => 'USSDPush'
                            ]
                        ]);
                        $results = $response->getBody()->getContents();
                        $data = json_decode($results, true);
                        if ($data['output_ResponseCode'] == 0) {
                            Enticement::where('msisdn',  $enticement->msisdn)
                                ->update([
                                    'count' => DB::raw('count+1'),
                                    'response' => $data['output_ResponseDesc']
                                ]);
                        }
                        return true;
                    } catch (\Throwable $th) {
                        Log::error($th->getMessage());
                        return true;
                    }
                }
            });
    }
}
