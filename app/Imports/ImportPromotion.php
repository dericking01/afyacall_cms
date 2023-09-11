<?php

namespace App\Imports;

use App\Models\Promotion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportPromotion implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Promotion([
            'name'    => $row['name'],
            'msisdn'    => $row['msisdn'],
            'status'    => $row['status'],
            'product_id'    => $row['product_id'],
        ]);
    }
}
