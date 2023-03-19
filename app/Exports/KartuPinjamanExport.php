<?php

namespace App\Exports;


use App\Managers\AngsuranManager;
use App\Managers\PinjamanManager;
use App\Models\Anggota;
use App\Models\JenisPinjaman;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KartuPinjamanExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
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

        $listPinjaman = PinjamanManager::getListPinjaman($anggota->kode_anggota,$from,$to)
            ->get();
        $awalPinjam = PinjamanManager::getListPinjamanSaldoAwal($anggota->kode_anggota,$thisYear)->get();
        $awalAngsur = AngsuranManager::getListAngsuranSaldoAwal($anggota->kode_anggota,$thisYear)->get();



        // data di grouping berdasarkan kode jenis simpan
        $groupedListPinjaman = $listPinjaman->groupBy('akun_debet');

        // kode_jenis_simpan yang wajib ada
        $jenisPinjaman = JenisPinjaman::orderBy('kode_jenis_pinjam', 'asc');
        $requiredKey = $jenisPinjaman->pluck('kode_jenis_pinjam');
        $requiredKeyIndex = $jenisPinjaman->pluck('kode_jenis_pinjam');

        // set default value untuk key yang tidak ada
        foreach ($requiredKey as $value) {
            if (!isset($groupedListPinjaman[$value])) {
                $groupedListPinjaman[$value] = collect([]);
            }
        }


        $PinjamanKeys = $groupedListPinjaman->keys();
//            $listPengambilan = Penarikan::where('kode_anggota', $anggota->kode_anggota)
//                ->whereYear('tgl_transaksi', $thisYear)
//                ->whereIn('code_trans', $simpananKeys)
//                ->whereraw('paid_by_cashier is not null')
//                ->orderBy('tgl_transaksi', 'asc')
//                ->get();
        $listAngsuran = AngsuranManager::getListAngsuran($anggota->kode_anggota,$from,$to)
            ->get();

        /*
            tiap jenis simpanan di bagi jadi 3 komponen
            1. saldo akhir tahun tiap jenis simpanan
            2. list simpanan untuk tiap jenis simpanan pada tahun ini
            3. jumlah simpanan untuk tiap jenis simpanan pada tahun ini
            4. nama jenis simpanan
            5. total saldo akhir tiap jenis simpanan
        */

        $listPinjaman = [];
        $index = count($requiredKey);
        foreach ($groupedListPinjaman as $key => $list) {
            $jenisPinjaman = JenisPinjaman::find($key);
            if ($jenisPinjaman) {
                $tabungan = $awalPinjam->where('akun_debet',$key)->sum('debet')-$awalAngsur->where('akun_kredit',$key)->sum('kredit');
                $transpinjam = $list->sum('debet');
                $transangsur = $listAngsuran->where('akun_kredit', $key)->values()->sum('kredit');
                $res['name'] = $jenisPinjaman->nama_pinjaman;
                $res['balance'] = ($tabungan) ? $tabungan + $transpinjam - $transangsur : $transpinjam - $transangsur;
                $res['list'] = $list;
                $res['amount'] = $list->sum('debet');
                $res['final_balance'] = $res['balance'] + $res['amount'];
                $res['withdrawalList'] = $listAngsuran->where('akun_kredit', $key)->values();
                $res['withdrawalAmount'] = $listAngsuran->where('akun_kredit', $key)->values()->sum('kredit');
                if (isset($requiredKeyIndex[$key])) {
                    $seq = $requiredKeyIndex[$key];
                    $listPinjaman[$seq] = (object)$res;
                } else {
                    $listPinjaman[$index] = (object)$res;
                    $index++;
                }
            }
        }


        $data['anggota'] = $anggota;
        $data['listPinjaman'] = collect($listPinjaman)->sortKeys();

        return view('pinjaman.card.detail', $data);
    }
}
