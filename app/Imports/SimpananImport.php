<?php

namespace App\Imports;

use App\Models\Simpanan;
use Carbon\Carbon;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class SimpananImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        if ($rowIndex == 1)
        {
            return null;
        }
        $col = explode(';',$row[0]);
        $fields = [
            'jenis_simpan' => ($col[0] == "\N" || $col[0] == '')? '':$col[0],
            'besar_simpanan' => ($col[1] == "\N" || $col[1] == '')? '':$col[1],
            'kode_anggota' => ($col[2] == "\N" || $col[2] == '')? '':$col[2],
            'u_entry' => ($col[3] == "\N" || $col[3] == '')? '':$col[3],
            'tgl_mulai' => ($col[4] == "\N" || $col[4] == '')? null:Carbon::createFromFormat('d/m/Y',$col[4]),
            'tgl_entri' => ($col[5] == "\N" || $col[5] == '')? '':Carbon::createFromFormat('d/m/Y',$col[5]),
            'kode_jenis_simpan' => ($col[6] == "\N" || $col[6] == '')? null:$col[6],
            'keterangan' => ($col[7] == "\N" || $col[7] == '')? null:$col[7],
        ];
        $simpanan = Simpanan::create($fields);
        return $simpanan;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    /*public function model(array $row)
    {
        $col = explode(';',$row[0]);
        $fields = [
            'jenis_simpan' => ($col[0] == "\N" || $col[0] == '')? '':$col[0],
            'besar_simpanan' => ($col[1] == "\N" || $col[1] == '')? '':$col[1],
            'kode_anggota' => ($col[2] == "\N" || $col[2] == '')? '':$col[2],
            'u_entry' => ($col[3] == "\N" || $col[3] == '')? '':$col[3],
            'tgl_mulai' => ($col[4] == "\N" || $col[4] == '')? null:Carbon::createFromFormat('d/m/Y',$col[4]),
            'tgl_entri' => ($col[5] == "\N" || $col[5] == '')? '':Carbon::createFromFormat('d/m/Y',$col[5]),
            'kode_jenis_simpan' => ($col[6] == "\N" || $col[6] == '')? null:$col[6],
            'keterangan' => ($col[7] == "\N" || $col[7] == '')? null:$col[7],
        ];
        // dd($fields)
        // dump($fields);
        return new Simpanan($fields);
    }*/
}
