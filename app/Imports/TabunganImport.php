<?php

namespace App\Imports;

use App\Models\Tabungan;
use App\Models\Simpanan;
use App\Models\JenisSimpanan;
use App\Managers\SimpananManager;
use App\Managers\JurnalManager;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
        try{
        $simpanan_exist= Simpanan::where('kode_anggota',$row[0])
                        ->where('kode_jenis_simpan',$row[1])
                        ->where('mutasi',1)->get();

                        if($simpanan_exist->count()>0){
                            foreach ($simpanan_exist as $simpanans) {
                                 $simpanans->delete();
                            }

                        }
            $tgl=Carbon::parse(Date::excelToDateTimeObject($row[3]));
        //pokok
            $nextSerialNumber = SimpananManager::getSerialNumber($tgl->format('d-m-Y'));
            $jenisSimpanan =  JenisSimpanan::where('kode_jenis_simpan',$row[1])->first();
            if(!$jenisSimpanan){
                Log::error('tesssstttttttt->'.$rowIndex);
            }else{
                $simpanan = new Simpanan();
                $simpanan->jenis_simpan = strtoupper($jenisSimpanan->nama_simpanan);
                $simpanan->besar_simpanan = $row[2];
                $simpanan->kode_anggota = $row[0];
                $simpanan->u_entry = Auth::user()->name;
                $simpanan->tgl_entri =  $tgl;
                $simpanan->tgl_transaksi =  $tgl;
                $simpanan->periode = $simpanan->tgl_entri;
                $simpanan->kode_jenis_simpan = $row[1];
                $simpanan->keterangan = 'Mutasi '.strtoupper($jenisSimpanan->nama_simpanan). ' '.$tgl->format('Y');
                $simpanan->id_akun_debet = null;
                $simpanan->serial_number = $nextSerialNumber;
                $simpanan->mutasi = 1;
                $simpanan->save();
                $feed=JurnalManager::createJurnalSaldoSimpanan($simpanan);
                if(!$feed){
                    throw new \Exception('Jurnal Failed row '.$rowIndex.' msg: '.$feed);
                }
                // dd($simpanan);
                return $simpanan;
            }
        }catch (\Exception $e) {

            throw new \Exception($e->getMessage());
        }


    }
}
