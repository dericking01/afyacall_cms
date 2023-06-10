<?php

namespace App\Imports;

use App\Models\Content;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContentImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Content([
            'content_type'    => $row['id'],
            'message'    => $row['message'],
            'eng_message'    => $row['engmessage'],
            'length'    => $row['priority'],
            'user_id'   => Auth::id(),
        ]);
    }
}
