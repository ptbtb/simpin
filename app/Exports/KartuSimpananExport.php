<?php

namespace App\Exports;

use App\Managers\PenarikanManager;
use App\Managers\SimpananManager;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KartuSimpananExport implements FromView, ShouldAutoSize
{
    public function __construct($kodeAnggota, Request $request)
    {
        $this->kodeAnggota = $kodeAnggota;
        $this->request = $request;
    }

    public function view(): View
    {
       // get anggota
        $anggota = Anggota::findOrFail($this->kodeAnggota);

        // get this year
        if (!$this->request->year) {
            $year = Carbon::today()->subYear()->endOfYear();
            $thisYear = Carbon::now()->year;
        } else {
            $year = Carbon::createFromFormat('Y', $this->request->year)->subYear()->endOfYear();
            $thisYear = Carbon::createFromFormat('Y', $this->request->year)->year;
        }
        $from = Carbon::createFromFormat('Y',$thisYear)->startOfYear()->format('Y-m-d');
        $to = Carbon::createFromFormat('Y',$thisYear)->endOfYear()->format('Y-m-d');

        // $thisYear = 2020;

        // get list simpanan by this year and kode anggota. sort by tgl_entry ascending

//            $listSimpanan = Simpanan::whereYear('periode', $thisYear)
//                ->where('kode_anggota', $anggota->kode_anggota)
//                ->where("mutasi", 0)
//                ->orderBy('periode', 'asc')
//                ->get();
        $listSimpanan = SimpananManager::getListSimpanan($anggota->kode_anggota,$from,$to)
            ->get();
        $awalSimpan = SimpananManager::getListSimpananSaldoAwal($anggota->kode_anggota,$thisYear)->get();
        $awaltarik = PenarikanManager::getListPenarikanSaldoAwal($anggota->kode_anggota,$thisYear)->get();



        // data di grouping berdasarkan kode jenis simpan
        $groupedListSimpanan = $listSimpanan->groupBy('akun_kredit');

        // kode_jenis_simpan yang wajib ada
        $jenisSimpanan = JenisSimpanan::orderBy('sequence', 'asc');
        $requiredKey = $jenisSimpanan->pluck('kode_jenis_simpan');
        $requiredKeyIndex = $jenisSimpanan->pluck('sequence', 'kode_jenis_simpan');

        // set default value untuk key yang tidak ada
        foreach ($requiredKey as $value) {
            if (!isset($groupedListSimpanan[$value])) {
                $groupedListSimpanan[$value] = collect([]);
            }
        }


        $simpananKeys = $groupedListSimpanan->keys();
//            $listPengambilan = Penarikan::where('kode_anggota', $anggota->kode_anggota)
//                ->whereYear('tgl_transaksi', $thisYear)
//                ->whereIn('code_trans', $simpananKeys)
//                ->whereraw('paid_by_cashier is not null')
//                ->orderBy('tgl_transaksi', 'asc')
//                ->get();
        $listPengambilan = PenarikanManager::getListPenarikan($anggota->kode_anggota,$from,$to)
            ->get();

        /*
            tiap jenis simpanan di bagi jadi 3 komponen
            1. saldo akhir tahun tiap jenis simpanan
            2. list simpanan untuk tiap jenis simpanan pada tahun ini
            3. jumlah simpanan untuk tiap jenis simpanan pada tahun ini
            4. nama jenis simpanan
            5. total saldo akhir tiap jenis simpanan
        */

        $listSimpanan = [];
        $index = count($requiredKey);
        foreach ($groupedListSimpanan as $key => $list) {
            $jenisSimpanan = JenisSimpanan::find($key);
            if ($jenisSimpanan) {
                $tabungan = $awalSimpan->where('akun_kredit',$key)->sum('kredit')-$awaltarik->where('akun_debet',$key)->sum('debet');

//                    $transsimpan = $anggota->listSimpanan
//                        ->where('kode_jenis_simpan', $key)
//                        ->where('periode', '<', $year)
//                        ->where('mutasi', 0)
//                        ->sum('besar_simpanan');
//                    $transtarik = $anggota->listPenarikan
//                        ->where('code_trans', $key)
//                        ->where('tgl_transaksi', '<', $year)
//                        ->wherenotnull('paid_by_cashier')
//                        ->sum('besar_ambil');
                $transsimpan = $list->sum('kredit');
                $transtarik = $listPengambilan->where('akun_debet', $key)->values()->sum('debet');
                $res['name'] = $jenisSimpanan->nama_simpanan;
                $res['balance'] = ($tabungan) ? $tabungan + $transsimpan - $transtarik : $transsimpan - $transtarik;
                $res['list'] = $list;
                $res['amount'] = $list->sum('kredit');
                $res['final_balance'] = $res['balance'] + $res['amount'];
                $res['withdrawalList'] = $listPengambilan->where('akun_debet', $key)->values();
                $res['withdrawalAmount'] = $listPengambilan->where('akun_debet', $key)->values()->sum('debet');
                if (isset($requiredKeyIndex[$key])) {
                    $seq = $requiredKeyIndex[$key];
                    $listSimpanan[$seq] = (object)$res;
                } else {
                    $listSimpanan[$index] = (object)$res;
                    $index++;
                }
            }
        }


        $data['anggota'] = $anggota;
        $data['listSimpanan'] = collect($listSimpanan)->sortKeys();
        return view('simpanan.card.detail', $data);
    }
}
