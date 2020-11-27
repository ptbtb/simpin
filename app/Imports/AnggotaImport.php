<?php

namespace App\Imports;

use App\Models\Anggota;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class AnggotaImport implements OnEachRow
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
        $kode_anggota = Anggota::max('kode_anggota')+1;
        $kode_tabungan = $kode_anggota;
        $user = Auth::user();
        $status = "aktif";
        $tgl_lahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4])->format('Y-m-d');
        $tgl_masuk = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[9])->format('Y-m-d');
        
        $fields = [
            'kode_anggota' => $kode_anggota,
            'kode_tabungan' => $kode_tabungan,
            'id_jenis_anggota' => $row[0],
            'nipp' =>  $row[1],
            'nama_anggota' => $row[2],
            'tempat_lahir' => $row[3],
            'tgl_lahir' => Carbon::createFromFormat('Y-m-d', $tgl_lahir),
            'jenis_kelamin' => $row[5],
            'alamat_anggota' => $row[6],
            'ktp' => $row[7],
            'lokasi_kerja' => $row[8],
            'tgl_masuk' => Carbon::createFromFormat('Y-m-d', $tgl_masuk),
            'email' => $row[10],
            'telp' => $row[11],
            'emergency_kontak' => $row[12],
            'no_rek' => $row[13],
            'u_entry' => $user->id,
            'status' => $status
        ];
        
        // if email or nipp is excist, next
        $anggota = Anggota::where('email',$fields['email'])
                            ->orWhere('nipp', $fields['nipp'])
                            ->first();
        if ($anggota)
        {
            return null;
        }
        
        return Anggota::create($fields);
    }
}
