<?php

namespace App\Imports;

use App\Models\Enticement;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Enticement([
            'msisdn'    => $row['contacts'],
            'user_id'   => Auth::id(),
        ]);
    }
}
