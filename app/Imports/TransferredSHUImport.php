<?php

namespace App\Imports;

use App\Models\TransferredSHU;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class TransferredSHUImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $value)
        {
            if ($key > 0)
            {
                $transferredSHU = new TransferredSHU();
                $transferredSHU->kode_anggota = $value[0];
                $transferredSHU->amount = $value[1];
                $transferredSHU->save();
            }
        }
    }
}
