<?php

namespace App\Imports;

use App\Models\Tabungan;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class TabunganImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        if ($rowIndex == 1)
        {
            return null;
        }

        $id = $row[0].str_replace('.','',$row[1]);
        $tabungan = Tabungan::find($id);
        if (is_null($tabungan))
        {
            $tabungan = new Tabungan();
            $tabungan->id = $id;
        }
        $tabungan->kode_tabungan = $row[0];
        $tabungan->kode_anggota = $row[0];
        $tabungan->batch = $row[3];
        $tabungan->besar_tabungan = $row[2];
        $tabungan->deskripsi = $row[4];
        $tabungan->kode_trans = $row[1];
        $tabungan->created_by = Auth::user()->name;
        $tabungan->updated_by = Auth::user()->name;
        $tabungan->save();
        return $tabungan;
    }
}
