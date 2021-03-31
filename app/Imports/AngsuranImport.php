<?php

namespace App\Imports;

use App\Managers\JurnalManager;
use App\Models\Angsuran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class AngsuranImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        if ($rowIndex == 1)
        {
            return null;
        }

        $angsuran = Angsuran::where('kode_pinjam', $row[0])
                            ->where('angsuran_ke', $row[1])
                            ->first();
        
        if ($angsuran)
        {
            $payDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[3])->format('Y-m-d');
            $pinjaman = $angsuran->pinjaman;
            $pembayaran = $row[2];
            if ($angsuran->besar_pembayaran) {
                $pembayaran = $pembayaran + $angsuran->besar_pembayaran;
            }
            if ($pembayaran >= $angsuran->totalAngsuran) {
                $angsuran->besar_pembayaran = $angsuran->totalAngsuran;
                $angsuran->id_status_angsuran = STATUS_ANGSURAN_LUNAS;
                $pinjaman->sisa_angsuran = $pinjaman->sisa_angsuran - 1;
                $pinjaman->save();
            } else {
                $angsuran->besar_pembayaran = $pembayaran;
            }


            $pembayaran = $pembayaran - $angsuran->totalAngsuran;
            $angsuran->paid_at = Carbon::createFromFormat('Y-m-d', $payDate);
            $angsuran->updated_by = Auth::user()->id;
            $angsuran->id_akun_kredit = null;
            $angsuran->save();

            // create JKM angsuran
            JurnalManager::createJurnalAngsuran($angsuran);

            if ($pembayaran <= 0) {
                $pinjaman->sisa_pinjaman = $angsuran->sisaPinjaman;
                $pinjaman->save();
            }
            if ($pinjaman->sisa_pinjaman <= 0) {
                $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
                $pinjaman->save();
            }
        }
    }
}
