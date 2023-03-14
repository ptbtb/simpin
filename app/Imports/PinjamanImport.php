<?php

namespace App\Imports;

use App\Models\Angsuran;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use App\Managers\AngsuranManager;
use App\Managers\PinjamanManager;
use App\Managers\JurnalManager;
use mysql_xdevapi\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PinjamanImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        try {
            $rowIndex = $row->getIndex();
            $row      = $row->toArray();
            if ($rowIndex == 1)
            {
                return null;
            }

            $besar_pinjam = $row[3];
            if ($besar_pinjam > 0 && $row[2] > 0)
            {
                // get next serial number
                $nextSerialNumber = PinjamanManager::getSerialNumberSA(Carbon::parse(Date::excelToDateTimeObject($row[8]))->format('d-m-Y'));

                $pinjaman = new Pinjaman();
                $kodeAnggota = $row[1];
                $kodePinjaman = str_replace('.', '', $row[0]) . '-' . $kodeAnggota . '-' . Carbon::parse(Date::excelToDateTimeObject($row[7]))->format('Ymd').$row[5];
                $pinjaman->kode_pinjam = $kodePinjaman;
                $check = Pinjaman::where('kode_pinjam',$kodePinjaman)->first();
                if($check){
                    throw new \Exception("Saldo Awal Pinjaman Sudah Ada, silahkan Hapus dahulu pinjaman lama");
                }
                $pinjaman->kode_pengajuan_pinjaman = $kodePinjaman;
                $pinjaman->kode_anggota = $kodeAnggota;
                $pinjaman->kode_jenis_pinjam = $row[0];
                $pinjaman->besar_pinjam = $besar_pinjam;
                $pinjaman->besar_angsuran_pokok = $row[4];
                $pinjaman->lama_angsuran = $row[2];
                $pinjaman->sisa_angsuran = $row[5];
                $pinjaman->sisa_pinjaman = $row[6] ;
                $pinjaman->biaya_jasa = 0;
                $pinjaman->besar_angsuran = 0;
                $pinjaman->biaya_asuransi = 0;
                $pinjaman->biaya_provisi = 0;
                $pinjaman->biaya_administrasi = 0;
                $pinjaman->u_entry = Auth::user()->name;
                $pinjaman->tgl_entri = Carbon::parse(Date::excelToDateTimeObject($row[7]));
                $pinjaman->tgl_transaksi = Carbon::parse(Date::excelToDateTimeObject($row[8]));
                $pinjaman->tgl_tempo = Carbon::parse(Date::excelToDateTimeObject($row[7]))->addMonths($row[2]+1)->format('Y-m-d');
                $pinjaman->tgl_mutasi = Carbon::parse(Date::excelToDateTimeObject($row[8]))->format('Y-m-d');
                $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_BELUM_LUNAS;
                $pinjaman->keterangan = 'Mutasi Saldo Awal Pinjaman';
                $pinjaman->serial_number_kredit = $nextSerialNumber;
                $pinjaman->saldo_mutasi =  $pinjaman->sisa_pinjaman;
                $pinjaman->mutasi_juli =  0;
                $pinjaman->save();

                JurnalManager::createJurnalSaldoPinjaman($pinjaman);
            }
        }catch (\Exception $e) {

            throw new \Exception($e->getMessage());
        }

    }
}
