<?php

namespace App\Repositories;

use App\Models\Enticement;
use Illuminate\Support\Facades\DB;

class EnticementRepository
{

    protected $enticement;

    public function __construct(Enticement $enticement)
    {
        $this->enticement = $enticement;
    }


    public function getAll()
    {
        return $this->enticement
            ->get();
    }

    public function getById($id)
    {
        return $this->enticement
            ->where('id', $id)
            ->get();
    }

    public function checkifexists($msisdn){

        return $this->enticement
                ->where('msisdn', $msisdn)
                ->exists();
    }

    public function updatetheincrementnumber($msisdn){

       return $this->enticement
            ->where('msisdn', $msisdn)
            ->update([
                'count' => DB::raw('count+1')
            ]);     
    }



    public function save($msisdn)
    {
        $enticement = new $this->enticement;
        $enticement->msisdn = $msisdn;
        $enticement->save();
        return $enticement->fresh();
    }

   
}
