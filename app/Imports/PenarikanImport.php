<?php

namespace App\Imports;

use App\Managers\JurnalManager;
use App\Managers\PenarikanManager;
use App\Models\Penarikan;
use Carbon\Carbon;
use App\Models\Code;
use Illuminate\Support\Facades\Auth;
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

        $tglAmbil = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[2])->format('Y-m-d');
        $fields = [
            'kode_anggota' => $row[0],
            'besar_ambil' => $row[1],
            'tgl_ambil' => Carbon::createFromFormat('Y-m-d', $tglAmbil),
            'keterangan' => $row[3],
            'code_trans' => $row[4],
            'id_bank' => $row[5],
        ];
        $nextSerialNumber = PenarikanManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
        $id_akun_debet = Code::where('CODE',$fields['id_bank'])->first();
        $penarikan = null;
        $penarikan = new Penarikan();
            $penarikan->kode_anggota = $fields['kode_anggota'];
            $penarikan->kode_tabungan = $fields['kode_anggota'];
            $penarikan->id_tabungan = $fields['kode_anggota'].$fields['code_trans'];
            $penarikan->besar_ambil = $fields['besar_ambil'];
            $penarikan->code_trans = $fields['code_trans'];
            $penarikan->tgl_ambil = $fields['tgl_ambil'];
            $penarikan->tgl_transaksi = $fields['tgl_ambil'];
            $penarikan->keterangan = $fields['keterangan'];
            $penarikan->id_akun_debet = $id_akun_debet->id;
            $penarikan->paid_by_cashier = Auth::user()->id;
            $penarikan->u_entry = Auth::user()->name;
            $penarikan->created_by = Auth::user()->id;
            $penarikan->status_pengambilan = 8;
            $penarikan->serial_number = $nextSerialNumber;
            $penarikan->save();
        JurnalManager::createJurnalPenarikan($penarikan);

        return $penarikan;
    }
}
