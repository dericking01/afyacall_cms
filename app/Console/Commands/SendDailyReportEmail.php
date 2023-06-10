<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Exports\TransactionExport;
use App\Mail\DailyReportMail;
use App\Mail\TicketMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;


class SendDailyReportEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Daily Report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $fileName = 'Daily Report As Of ' . Carbon::yesterday()->isoFormat('LL') . '.csv';
        $fileName = 'Daily.csv';

        // Excel::store(new TransactionExport, $fileName);

        $path = storage_path('app\public\\'.$fileName);

        Log::info($path);

        $data["email"] = "julius.john@it.co.tz";
        $data["title"] = "Daily Reports";
 
        $files = [
            public_path('storage/Daily.csv')
        ];
  
        Mail::send('emails.test', $data, function($message){
            $message->to('john.haule@it.co.tz', 'Recipirnt Name');
            $message->subject("Test Mail");
        });
        // Mail::to('julius.john@it.co.tz')
        //     ->send(new DailyReportMail);

        // Mail::send('admin.reports.emaildaily', $data, function($message)use($data, $files) {
        //     $message->to($data["email"])
        //             ->subject($data["title"]);
 
        //     foreach ($files as $file){
        //         $message->attach($file);
        //     }            
        // });

        echo "Mail send successfully !!";


    //     try {

    //      Mail::to('julius.john@it.co.tz')
    //          ->send(new DailyReportMail);


    //    } catch (\Throwable $th) {

    //    }
   
      

        // return 0;
    }
}
