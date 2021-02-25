<?php

namespace App\Imports;

use App\Events\Anggota\AnggotaCreated;
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
        
        $kode_anggota = $row[0];
        $kode_tabungan = $row[1];
        $user = Auth::user();
        $status = $row[16];
        $tgl_lahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[6])->format('Y-m-d');
        $tgl_masuk = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[11])->format('Y-m-d');
        
        $fields = [
            'kode_anggota' => $kode_anggota,
            // 'kode_tabungan' => $kode_tabungan,
            'id_jenis_anggota' => $row[2],
            'nipp' =>  $row[3],
            'nama_anggota' => $row[4],
            'tempat_lahir' => $row[5],
            'tgl_lahir' => Carbon::createFromFormat('Y-m-d', $tgl_lahir),
            'jenis_kelamin' => $row[7],
            'alamat_anggota' => $row[8],
            'ktp' => $row[9],
            'lokasi_kerja' => $row[10],
            'tgl_masuk' => Carbon::createFromFormat('Y-m-d', $tgl_masuk),
            'email' => $row[12],
            'telp' => $row[13],
            'emergency_kontak' => $row[14],
            'no_rek' => $row[15],
            'u_entry' => $user->id,
            'status' => $status
        ];
        
        // if email or nipp is excist, next
        $anggota = Anggota::where('kode_anggota', $fields['kode_anggota'])
                            ->first();

        if ($anggota)
        {
            return null;
        }

        $anggota = Anggota::create($fields);
        $password =  Hash::make(uniqid());
        event(new AnggotaCreated($anggota,$password));
    }
}
