<?php

namespace App\Imports;

use App\Models\Angsuran;
use App\Models\Pinjaman;
use App\Models\JenisPinjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use App\Managers\AngsuranManager;
use App\Managers\PinjamanManager;
use App\Managers\JurnalManager;
use App\Models\Code;

class PinjamanBaruImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        if ($rowIndex == 1)
        {
            return null;
        }

        $besar_pinjam = $row[2];
        $code = Code::where('CODE',$row[8])->first();
        if ($code){
            $id_akun_debet=$code->id;
        }else{
           throw new \Exception('COA pada baris '.$rowIndex.' tidak ada dalam database');
        }
        
        if ($besar_pinjam > 0 )
        {
            // get next serial number
            $nextSerialNumber = PinjamanManager::getSerialNumber(Carbon::now()->format('d-m-Y'));

            $postDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[7])->format('Y-m-d');
            $jenis= JenisPinjaman::find($row[0]);
            $lamaangsuran= $jenis->lama_angsuran;

            $pinjaman = new Pinjaman();
            $kodeAnggota = $row[1];
            $kodePinjaman = str_replace('.', '', $row[0]) . '-' . $kodeAnggota . '-' . Carbon::createFromFormat('Y-m-d', $postDate);
            $pinjaman->kode_pinjam = $kodePinjaman;
            $pinjaman->kode_pengajuan_pinjaman = $kodePinjaman;
            $pinjaman->kode_anggota = $kodeAnggota;
            $pinjaman->kode_jenis_pinjam = $row[0];
            $pinjaman->besar_pinjam = $besar_pinjam;
            $pinjaman->besar_angsuran_pokok = $besar_pinjam / $lamaangsuran;
            $pinjaman->lama_angsuran = $lamaangsuran;
            $pinjaman->sisa_angsuran = $lamaangsuran;
            $pinjaman->sisa_pinjaman = $besar_pinjam;
            $pinjaman->biaya_jasa = $row[3];
            $pinjaman->besar_angsuran = $row[3] + $pinjaman->besar_angsuran_pokok;
            $pinjaman->biaya_asuransi = $row[4];
            $pinjaman->biaya_provisi = $row[5];
            $pinjaman->biaya_administrasi = $row[6];
            $pinjaman->id_akun_debet = $id_akun_debet;
            $pinjaman->u_entry = Auth::user()->name;
            $pinjaman->tgl_entri = Carbon::createFromFormat('Y-m-d', $postDate);
            if($jenis->kode_jenis_pinjam=='105.01.000'){
                $pinjaman->tgl_tempo = Carbon::createFromFormat('Y-m-d', $postDate)->addMonths(3 - 1);
            }else{
                $pinjaman->tgl_tempo = Carbon::createFromFormat('Y-m-d', $postDate)->addMonths($lamaangsuran - 1);
            }

            $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_BELUM_LUNAS;
            $pinjaman->keterangan = 'Mutasi  Pinjaman Simkop';
            $pinjaman->serial_number = $nextSerialNumber;
            $pinjaman->save();



            for ($i = 0; $i <= $pinjaman->sisa_angsuran - 1; $i++)
            {
                // get next serial number
                $nextSerialNumber = AngsuranManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
                if($jenis->kode_jenis_pinjam=='105.01.000'){
                    $jatuhTempo = $pinjaman->tgl_entri->addMonths(3)->endOfMonth();
                }else{
                    $jatuhTempo = $pinjaman->tgl_entri->addMonths($i)->endOfMonth();
                }

                $sisaPinjaman = $pinjaman->sisa_pinjaman;
                $angsuran = new Angsuran();
                $angsuran->kode_pinjam = $pinjaman->kode_pinjam;
                $angsuran->angsuran_ke = $pinjaman->lama_angsuran - $pinjaman->sisa_angsuran + $i + 1;
                $angsuran->besar_angsuran = $pinjaman->besar_angsuran_pokok;
                $angsuran->denda = 0;
                $angsuran->jasa = $pinjaman->biaya_jasa;
                $angsuran->kode_anggota = $pinjaman->kode_anggota;
                $angsuran->sisa_pinjam = $sisaPinjaman;
                $angsuran->tgl_entri = Carbon::createFromFormat('Y-m-d', $postDate);
                $angsuran->jatuh_tempo = $jatuhTempo;
                $angsuran->u_entry = Auth::user()->name;
                $angsuran->serial_number = $nextSerialNumber;
                $angsuran->save();
            }
            JurnalManager::createJurnalPinjaman($pinjaman);
        }
    }
}
