<?php

namespace App\Imports;

use App\Models\Blacklist;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BlacklistImport implements ToModel, WithHeadingRow
{

    public $data;
    public function __construct()
    {
        $this->data = collect();
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        //return an eloquent object
        $model = Blacklist::firstOrCreate([
            'msisdn' => $row['msisdn'],
        ], [
            'reason'    => $row['reason'],
            'user_id'   => Auth::id()
        ]);

        return $model;
    }
}

