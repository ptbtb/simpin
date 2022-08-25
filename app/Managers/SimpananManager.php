<?php
namespace App\Managers;

use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\Pengajuan;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SimpananManager
{
    static function penarikanApproved(Penarikan $penarikan)
    {
        try
        {
            $thisYear = Carbon::now()->year;
            $listSimpanan = Simpanan::where('kode_anggota', $penarikan->kode_anggota)
                                    ->whereYear('tgl_entri', $thisYear)
                                    ->where('kode_jenis_simpan',$penarikan->code_trans)
                                    ->get();

            $totalTarik = $penarikan->besar_ambil;
            foreach ($listSimpanan as $simpanan)
            {
                if ($simpanan->besar_simpanan > $totalTarik)
                {
                    $simpanan->besar_simpanan = $simpanan->besar_simpanan - $totalTarik;
                    $simpanan->save();
                    $totalTarik = 0;
                }
                else
                {
                    $totalTarik = $totalTarik - $simpanan->besar_simpanan;
                    $simpanan->besar_simpanan = 0;
                    $simpanan->save();
                }

                if ($totalTarik == 0)
                {
                    break;
                }
            }

            if ($totalTarik > 0)
            {
                // $tabungan = $penarikan->tabungan;
                // $tabungan->besar_tabungan = $tabungan->besar_tabungan - $totalTarik;
                // $tabungan->save();
                $totalTarik = 0;
            }
        }
        catch (\Throwable $e)
        {
            Log::error($e);
        }
    }

    /**
     * get serial number on simpanan table.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public static function getSerialNumber($date)
    {
        try
        {
            $nextSerialNumber = 1;

            // get date
            $date = Carbon::createFromFormat('d-m-Y', $date);
            $year = $date->year;

            // get simpanan data on this year
            $lastSimpanan = Simpanan::whereYear('tgl_entri', '=', $year)
                                        ->orderBy('serial_number', 'desc')
                                        ->first();
            if($lastSimpanan)
            {
                $nextSerialNumber = $lastSimpanan->serial_number + 1;
            }

            return $nextSerialNumber;
        }
        catch(\Exception $e)
        {
            \Log::info($e->getMessage());
            return false;
        }
    }

    public static function generateMutasiSimpananAnggota()
    {
        $jenisSimpanan = JenisSimpanan::all();
        $anggotas = Anggota::doesntHave('simpanSaldoAwal')
                            ->get();

        foreach ($anggotas as $anggota)
        {
            $jenisSimpanan->each(function ($jenis) use ($anggota)
            {
                $simpanan = new Simpanan();
                $simpanan->jenis_simpan = $jenis->nama_simpanan;
                $simpanan->besar_simpanan = 0;
                $simpanan->kode_anggota = $anggota->kode_anggota;
                $simpanan->u_entry = 'SYSTEM';
                $simpanan->periode = Carbon::now();
                $simpanan->tgl_mulai = Carbon::now();
                $simpanan->tgl_transaksi = Carbon::now();
                $simpanan->tgl_entri = Carbon::now();
                $simpanan->kode_jenis_simpan = $jenis->kode_jenis_simpan;
                $simpanan->keterangan = "MUTASI ".$jenis->nama_simpanan." ". Carbon::now()->year;
                $simpanan->mutasi = 1;
                $simpanan->save();
            });
        }
    }
    public static function createSaldoAwal(Anggota $anggota)
    {
        $jenisSimpanan = JenisSimpanan::all();



            $jenisSimpanan->each(function ($jenis) use ($anggota)
            {
                $simpanan = new Simpanan();
                $simpanan->jenis_simpan = $jenis->nama_simpanan;
                $simpanan->besar_simpanan = 0;
                $simpanan->kode_anggota = $anggota->kode_anggota;
                $simpanan->u_entry = 'SYSTEM';
                $simpanan->periode = Carbon::now();
                $simpanan->tgl_mulai = Carbon::now();
                $simpanan->tgl_transaksi = Carbon::now();
                $simpanan->tgl_entri = Carbon::now();
                $simpanan->kode_jenis_simpan = $jenis->kode_jenis_simpan;
                $simpanan->keterangan = "Anggota Baru ".$jenis->nama_simpanan." ". Carbon::now()->year;
                $simpanan->mutasi = 1;
                $simpanan->save();
            });

    }

    public static function createSimpananPagu(Pengajuan $pengajuan)
    {
        $jenisSimpanan = JenisSimpanan::khususPagu()->first();
        $nextSerialNumber = self::getSerialNumber(Carbon::now()->format('d-m-Y'));

        $simpanan = new Simpanan();
        $simpanan->jenis_simpan = strtoupper($jenisSimpanan->nama_simpanan);
        $simpanan->besar_simpanan = $pengajuan->transfer_simpanan_pagu;
        $simpanan->kode_anggota = $pengajuan->kode_anggota;
        $simpanan->u_entry = Auth::user()->name;
        $simpanan->tgl_entri = $pengajuan->tgl_transaksi;
        $simpanan->tgl_transaksi = $pengajuan->tgl_transaksi;
        $simpanan->periode = $pengajuan->tgl_transaksi;
        $simpanan->kode_jenis_simpan = $jenisSimpanan->kode_jenis_simpan;
        $simpanan->keterangan = "Simpanan pagu dari pengajuan pinjaman ". $pengajuan->kode_pengajuan;
        $simpanan->id_akun_debet = $pengajuan->id_akun_debet;
        $simpanan->serial_number = $nextSerialNumber;
        $simpanan->save();

        JurnalManager::createJurnalSimpanan($simpanan);
    }

    public static function createSimpananPaguTanpaJurnal(Pengajuan $pengajuan)
    {
        $jenisSimpanan = JenisSimpanan::khususPagu()->first();
        $nextSerialNumber = self::getSerialNumber(Carbon::now()->format('d-m-Y'));

        $simpanan = new Simpanan();
        $simpanan->jenis_simpan = strtoupper($jenisSimpanan->nama_simpanan);
        $simpanan->besar_simpanan = $pengajuan->transfer_simpanan_pagu;
        $simpanan->kode_anggota = $pengajuan->kode_anggota;
        $simpanan->u_entry = Auth::user()->name;
        $simpanan->tgl_entri = $pengajuan->tgl_transaksi;
        $simpanan->tgl_transaksi = $pengajuan->tgl_transaksi;
        $simpanan->periode = $pengajuan->tgl_transaksi;
        $simpanan->kode_jenis_simpan = $jenisSimpanan->kode_jenis_simpan;
        $simpanan->keterangan = "Simpanan pagu dari pengajuan pinjaman ". $pengajuan->kode_pengajuan;
        $simpanan->id_akun_debet = $pengajuan->id_akun_debet;
        $simpanan->serial_number = $nextSerialNumber;
        $simpanan->save();

//        JurnalManager::createJurnalSimpanan($simpanan);
    }
}

?>
