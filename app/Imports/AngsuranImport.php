<?php

namespace App\Imports;

use App\Managers\JurnalManager;
use App\Managers\AngsuranManager;
use App\Models\Angsuran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;
use App\Models\Code;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Illuminate\Database\Eloquent\ModelNotfoundException;
use Illuminate\Support\Facades\Log;

class AngsuranImport 
{
    static function generatetransaksi($transaksi)
    {
        \Log::info($transaksi['kode pinjam']);
        $tgl_bayar =  Carbon::parse($transaksi['tanggal bayar'])->format('Y-m-d');
        \Log::info($tgl_bayar);;


        $angsuran = Angsuran::where('kode_pinjam', $transaksi['kode pinjam'])
                            ->where('angsuran_ke', $transaksi['angsuran ke'])
                            ->first();
        $idkredit=($transaksi['coa'] == "\N" || $transaksi['coa'] == '' || $transaksi['coa'] == null) ? null : $transaksi['coa'];
        $idakunkredit=Code::where('CODE',$idkredit)->first();
        if (!$idakunkredit){
             throw new ModelNotfoundException('Coa '.$idkredit.' untuk pinjaman '.$transaksi['kode pinjam'].' tidak ada dalam database');
        }
        if (!$angsuran){
             throw new ModelNotfoundException('Angsuran ke '.$transaksi['angsuran ke'].' untuk pinjaman '.$transaksi['kode pinjam'].' tidak ada dalam database');
        }
        
            $payDate =$tgl_bayar;
            $serialNumber=AngsuranManager::getSerialNumber($transaksi['tanggal bayar']->format('d-m-Y'));
            
            $pinjaman = $angsuran->pinjaman;
            $pembayaran = $transaksi['jumlah bayar'];
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
            $angsuran->paid_at = $payDate;
            $angsuran->updated_by = Auth::user()->id;
            $angsuran->id_akun_kredit = ($idakunkredit->id) ? $idakunkredit->id : null;
            $angsuran->serial_number=$serialNumber;
            $angsuran->tgl_transaksi=$payDate;
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
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        if ($rowIndex == 1)
        {
            return null;
        }

        $angsuran = Angsuran::where('kode_pinjam', $transaksi[0])
                            ->where('angsuran_ke', $transaksi[1])
                            ->first();
        $idkredit=($transaksi[4] == "\N" || $transaksi[4] == '' || $transaksi[4] == null) ? null : $transaksi[4];
        $idakunkredit=Code::where('CODE',$idkredit)->first();
        
        if ($angsuran)
        {
            $payDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($transaksi[3])->format('Y-m-d');
            $serialNumber=AngsuranManager::getSerialNumber(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($transaksi[3])->format('d-m-Y'));
            
            $pinjaman = $angsuran->pinjaman;
            $pembayaran = $transaksi[2];
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
            $angsuran->id_akun_kredit = ($idakunkredit->id) ? $idakunkredit->id : null;
            $angsuran->serial_number=$serialNumber;
            $angsuran->tgl_transaksi=$paid_at;
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
