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
       $anggota = Anggota::with('simpanSaldoAwal')->findOrFail($this->kodeAnggota);

       // get this year
      if(!$request->year){
                    $year= Carbon::today()->subYear()->endOfYear();
                    $thisYear = Carbon::now()->year;
                }else{
                    $year= Carbon::createFromFormat('Y',$request->year)->subYear()->endOfYear();
                    $thisYear = Carbon::createFromFormat('Y',$request->year)->year;
                }

       // get list simpanan by this year and kode anggota. sort by tgl_entry ascending
        $listSimpanan = Simpanan::whereYear('tgl_entri', $thisYear)
            ->where('kode_anggota', $anggota->kode_anggota)
            ->where("mutasi",0)
            ->orderBy('tgl_entri', 'asc')
            ->get();

       // data di grouping berdasarkan kode jenis simpan
       $groupedListSimpanan = $listSimpanan->groupBy('kode_jenis_simpan');

       // kode_jenis_simpan yang wajib ada
       $jenisSimpanan = JenisSimpanan::orderBy('sequence', 'asc');
       $requiredKey = $jenisSimpanan->pluck('kode_jenis_simpan');
       $requiredKeyIndex = $jenisSimpanan->pluck('sequence','kode_jenis_simpan');

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
                                   ->whereraw('paid_by_cashier is not null')
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
               $tabungan = $anggota->simpanSaldoAwal->where('kode_trans',$key)->first();
               $transsimpan = $anggota->listSimpanan
                                    ->where('kode_jenis_simpan', $key)
                                    ->where('periode','<',$year)
                                    ->where('mutasi',0)
                                    ->sum('besar_simpanan');
                        $transtarik = $anggota->listPenarikan
                                    ->where('code_trans', $key)
                                    ->where('tgl_ambil','<',$year)
                                    ->wherenotnull('paid_by_cashier')
                                    ->sum('besar_ambil');
               $res['name'] = $jenisSimpanan->nama_simpanan;
               $res['balance'] = ($tabungan)? $tabungan->besar_tabungan+$transsimpan-$transtarik:0;
               $res['list'] = $list;
               $res['amount'] = $list->sum('besar_simpanan');
               $res['final_balance'] = $res['balance'] + $res['amount'];
               $res['withdrawalList'] = $listPengambilan->where('code_trans', $key)->values();
               $res['withdrawalAmount'] = $listPengambilan->where('code_trans', $key)->values()->sum('besar_ambil');
               if (isset($requiredKeyIndex[$key]))
               {
                   $seq = $requiredKeyIndex[$key];
                   $listSimpanan[$seq] = (object)$res;
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
