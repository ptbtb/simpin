<?php

namespace App\Imports;

use App\Models\Angsuran;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class PinjamanImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        if ($rowIndex == 1)
        {
            return null;
        }
        
        $besar_pinjam = $row[3];
        if ($besar_pinjam > 0 && $row[2] > 0)
        {
            $pinjaman = new Pinjaman();
            $kodeAnggota = $row[1];
            $kodePinjaman = str_replace('.', '', $row[0]) . '-' . $kodeAnggota . '-' . Carbon::createFromFormat('Y-m-d', $row[0]);
            $pinjaman->kode_pinjam = $kodePinjaman;
            $pinjaman->kode_pengajuan_pinjaman = $kodePinjaman;
            $pinjaman->kode_anggota = $kodeAnggota;
            $pinjaman->kode_jenis_pinjam = $row[0];
            $pinjaman->besar_pinjam = $besar_pinjam;
            $pinjaman->besar_angsuran_pokok = $besar_pinjam / $row[2];
            $pinjaman->lama_angsuran = $row[2];
            $pinjaman->sisa_angsuran = $row[5];
            $pinjaman->sisa_pinjaman = $row[5] * $pinjaman->besar_angsuran_pokok;
            $pinjaman->biaya_jasa = $row[4];
            $pinjaman->besar_angsuran = $row[4] + $pinjaman->besar_angsuran_pokok;
            $pinjaman->biaya_asuransi = 0;
            $pinjaman->biaya_provisi = 0;
            $pinjaman->biaya_administrasi = 0;
            $pinjaman->u_entry = Auth::user()->name;
            $pinjaman->tgl_entri = Carbon::now();
            $pinjaman->tgl_tempo = Carbon::now()->addMonths($row[5] - 1);
            $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_BELUM_LUNAS;
            $pinjaman->keterangan = 'Mutasi Saldo Awal Pinjaman';
            $pinjaman->save();


            for ($i = 0; $i <= $pinjaman->sisa_angsuran - 1; $i++)
            {
                $jatuhTempo = $pinjaman->tgl_entri->addMonths($i)->endOfMonth();
                $sisaPinjaman = $pinjaman->sisa_pinjaman;
                $angsuran = new Angsuran();
                $angsuran->kode_pinjam = $pinjaman->kode_pinjam;
                $angsuran->angsuran_ke = $pinjaman->lama_angsuran - $pinjaman->sisa_angsuran + $i + 1;
                $angsuran->besar_angsuran = $pinjaman->besar_angsuran_pokok;
                $angsuran->denda = 0;
                $angsuran->jasa = $pinjaman->biaya_jasa;
                $angsuran->kode_anggota = $pinjaman->kode_anggota;
                $angsuran->sisa_pinjam = $sisaPinjaman;
                $angsuran->tgl_entri = Carbon::now();
                $angsuran->jatuh_tempo = $jatuhTempo;
                $angsuran->u_entry = Auth::user()->name;
                $angsuran->save();
            }
        }
    }
}
