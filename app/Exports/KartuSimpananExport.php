<?php

namespace App\Exports;

use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KartuSimpananExport implements FromView, ShouldAutoSize
{
    public function __construct($kodeAnggota)
    {
        $this->kodeAnggota = $kodeAnggota;
    }

    public function view(): View
    {
       // get anggota
       $anggota = Anggota::findOrFail($this->kodeAnggota);

       // get this year
       $thisYear = Carbon::now()->year;

       // get list simpanan by this year and kode anggota. sort by tgl_entry ascending
       $listSimpanan = Simpanan::whereYear('tgl_entri', $thisYear)
                               ->where('kode_anggota', $anggota->kode_anggota)
                               ->orderBy('tgl_entri','asc')
                               ->get();
                               
       // data di grouping berdasarkan kode jenis simpan
       $groupedListSimpanan = $listSimpanan->groupBy('kode_jenis_simpan');

       // kode_jenis_simpan yang wajib ada
       $requiredKey = collect([JENIS_SIMPANAN_POKOK, JENIS_SIMPANAN_WAJIB, JENIS_SIMPANAN_SUKARELA]);

       // set default value untuk key yang tidak ada
       foreach ($requiredKey as $value)
       {
           if (!isset($groupedListSimpanan[$value]))
           {
               $groupedListSimpanan[$value] = collect([]);
           }
       }

       $simpananKeys = $groupedListSimpanan->keys();
       $listPengambilan = Penarikan::where('kode_anggota', $anggota->kode_anggota)
                                   ->whereYear('tgl_ambil', $thisYear)
                                   ->whereIn('code_trans', $simpananKeys)
                                   ->orderBy('tgl_ambil', 'asc')
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
       foreach ($groupedListSimpanan as $key => $list)
       {
           $jenisSimpanan = JenisSimpanan::find($key);
           if ($jenisSimpanan)
           {
               $res['name'] = $jenisSimpanan->nama_simpanan;
               $res['balance'] = 5000000;
               $res['list'] = $list;
               $res['amount'] = $list->sum('besar_simpanan');
               $res['final_balance'] = $res['balance'] + $res['amount'];
               $res['withdrawalList'] = $listPengambilan->where('code_trans', $key)->values();
               $res['withdrawalAmount'] = $listPengambilan->where('code_trans', $key)->values()->sum('besar_ambil');
               if ($key == JENIS_SIMPANAN_POKOK)
               {
                   $listSimpanan[0] = (object)$res;
               }
               else if($key == JENIS_SIMPANAN_WAJIB)
               {
                   $listSimpanan[1] = (object)$res;
               }
               else if($key == JENIS_SIMPANAN_SUKARELA)
               {
                   $listSimpanan[2] = (object)$res;
               }
               else
               {
                   $listSimpanan[$index] = (object)$res;
                   $index++;
               }
           }
       }
                               
       $data['anggota'] = $anggota;
       $data['listSimpanan'] = collect($listSimpanan)->sortKeys();
        return view('simpanan.card.export2', $data);
    }
}
