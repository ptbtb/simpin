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

class PenarikanImport 
{
    /**
    * @param Collection $collection
    */
    static function generatetransaksi($transaksi)
    {
       
         \Log::info($transaksi['tgl_ambil']->format('Y-m-d'));
        $tglAmbil = $transaksi['tgl_ambil']->format('Y-m-d');
        $fields = [
            'kode_anggota' => $transaksi['kode_anggota'],
            'besar_ambil' => $transaksi['besar_ambil'],
            'tgl_ambil' => $tglAmbil,
            'keterangan' => $transaksi['keterangan'],
            'code_trans' => $transaksi['code_trans'],
            'id_bank' => $transaksi['coa bank/cash'],
        ];
        // dd($fields);
        $nextSerialNumber = PenarikanManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
        $id_akun_debet = Code::where('CODE',$fields['id_bank'])->first();
        $penarikan = null;
        $cek = Penarikan::where('kode_anggota',$fields['kode_anggota'])
                            ->where ('tgl_ambil',$fields['besar_ambil'])
                            ->where ('besar_ambil',$fields['besar_ambil'])
                            ->where('code_trans',$fields['code_trans'])
                            ->where('id_akun_debet',$id_akun_debet->id)->first();

        if ($cek){
            $cek->kode_anggota = $fields['kode_anggota'];
            $cek->kode_tabungan = $fields['kode_anggota'];
            $cek->id_tabungan = $fields['kode_anggota'].$fields['code_trans'];
            $cek->besar_ambil = $fields['besar_ambil'];
            $cek->code_trans = $fields['code_trans'];
            $cek->tgl_ambil = $fields['tgl_ambil'];
            $cek->tgl_transaksi = $fields['tgl_ambil'];
            $cek->keterangan = $fields['keterangan'];
            $cek->id_akun_debet = $id_akun_debet->id;
            $cek->paid_by_cashier = Auth::user()->id;
            $cek->u_entry = Auth::user()->name;
            $cek->created_by = Auth::user()->id;
            $cek->status_pengambilan = 8;
            $cek->serial_number = $nextSerialNumber;
            $cek->save();
            if ($cek->jurnals->count()==0){
                JurnalManager::createJurnalPenarikan($cek);
            }
        }else{
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

       
        }
         return $penarikan;
        
    }
}
