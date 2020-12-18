<?php

namespace App\Imports;

use App\Models\Penarikan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class PenarikanImport implements OnEachRow
{
    /**
    * @param Collection $collection
    */
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        if ($rowIndex == 1)
        {
            return null;
        }

        $tglAmbil = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[3])->format('Y-m-d');
        $fields = [
            'kode_anggota' => $row[0],
            'kode_tabungan' => $row[1],
            'besar_ambil' => $row[2],
            'tgl_ambil' => Carbon::createFromFormat('Y-m-d', $tglAmbil),
            'keterangan' => $row[4],
            'code_trans' => $row[5],
            'u_entry' => $row[6],
        ];
        $penarikan = null;
        DB::transaction(function () use ($fields, &$penarikan)
        {
            $penarikan = Penarikan::create($fields);
        });

        return $penarikan;
    }
}
