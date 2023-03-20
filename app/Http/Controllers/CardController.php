<?php

namespace App\Http\Controllers;

use App\Exports\KartuSimpananExport;
use App\Managers\AngsuranManager;
use App\Managers\PenarikanManager;
use App\Managers\PinjamanManager;
use App\Managers\SimpananManager;
use App\Models\Anggota;
use App\Models\JenisPinjaman;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class CardController extends Controller
{
    public function indexCard(Request $request)
    {
        try {
            if ($request->kode_anggota) {
                $data['anggota'] = Anggota::find($request->kode_anggota);
            }
            $listtahun = [
                Carbon::now()->addYear()->format('Y'),
                Carbon::now()->format('Y'),
                Carbon::now()->subYear()->format('Y'),
            ];
            if (!$request->year) {
                $tahun = $listtahun[1];
            } else {
                $tahun = Carbon::createFromFormat('Y-m-d', $request->year)->format('Y');
            }

            $data['title'] = "Kartu Simpanan";
            $data['listtahun'] = $listtahun;
            $data['tahun'] = $tahun;
            $data['request'] = $request;
            return view('simpanan.card.index', $data);
        } catch (\Throwable $e) {
            \Log::error($e);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }

    public function downloadExcelCard($kodeAnggota, Request $request)
    {
        try {
            $filename = 'export_kartu_simpanan_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
            return Excel::download(new KartuSimpananExport($kodeAnggota, $request), $filename);
        } catch (\Throwable $th) {
            \Log::error($th);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }
    public function downloadPDFCard(Request $request, $kodeAnggota)
    {
        try {
            // get anggota
            $anggota = Anggota::findOrFail($kodeAnggota);

            // get this year
            if (!$request->year) {
                $year = Carbon::today()->subYear()->endOfYear();
                $thisYear = Carbon::now()->year;
            } else {
                $year = Carbon::createFromFormat('Y', $request->year)->subYear()->endOfYear();
                $thisYear = Carbon::createFromFormat('Y', $request->year)->year;
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
                    $res['balance'] = $tabungan ;
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
            // dd($data);
            // share data to view
            view()->share('data', $data);
            PDF::setOptions(['margin-left' => 0, 'margin-right' => 0]);
            $pdf = PDF::loadView('simpanan.card.detail', $data)->setPaper('a4', 'portrait');

            // download PDF file with download method
            $filename = 'export_kartu_simpanan_' . Carbon::now()->format('d M Y') . '.pdf';
            return $pdf->download($filename);
        } catch (\Throwable $e) {
            \Log::error($e);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }
    public function showCard(Request $request, $kodeAnggota)
    {
        try {
            // get anggota
            $anggota = Anggota::findOrFail($kodeAnggota);

            // get this year
            if (!$request->year) {
                $year = Carbon::today()->subYear()->endOfYear();
                $thisYear = Carbon::now()->year;
            } else {
                $year = Carbon::createFromFormat('Y', $request->year)->subYear()->endOfYear();
                $thisYear = Carbon::createFromFormat('Y', $request->year)->year;
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
                    $res['balance'] = $tabungan;
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
//             dd($data['listSimpanan']);

            return view('simpanan.card.detail', $data);
        } catch (\Throwable $e) {
            \Log::error($e);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }


    // pinjaman section

    public function indexCardPinjaman(Request $request)
    {
        try {
            if ($request->kode_anggota) {
                $data['anggota'] = Anggota::find($request->kode_anggota);
            }
            $listtahun = [
                Carbon::now()->addYear()->format('Y'),
                Carbon::now()->format('Y'),
                Carbon::now()->subYear()->format('Y'),
            ];
            if (!$request->year) {
                $tahun = $listtahun[1];
            } else {
                $tahun = Carbon::createFromFormat('Y-m-d', $request->year)->format('Y');
            }

            $data['title'] = "Kartu Pinjaman";
            $data['listtahun'] = $listtahun;
            $data['tahun'] = $tahun;
            $data['request'] = $request;
            return view('pinjaman.card.index', $data);
        } catch (\Throwable $e) {
            \Log::error($e);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }

    public function showCardPinjaman(Request $request, $kodeAnggota)
    {
        try {
            // get anggota
            $anggota = Anggota::findOrFail($kodeAnggota);

            // get this year
            if (!$request->year) {
                $year = Carbon::today()->subYear()->endOfYear();
                $thisYear = Carbon::now()->year;
            } else {
                $year = Carbon::createFromFormat('Y', $request->year)->subYear()->endOfYear();
                $thisYear = Carbon::createFromFormat('Y', $request->year)->year;
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
                    $res['balance'] = $tabungan;
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
//             dd($data['listSimpanan']);

            return view('pinjaman.card.detail', $data);
        } catch (\Throwable $e) {
            \Log::error($e);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }
    public function downloadExcelCardPinjaman($kodeAnggota, Request $request)
    {
        try {
            $filename = 'export_kartu_pinjaman_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
            return Excel::download(new KartuPinjamanExport($kodeAnggota, $request), $filename);
        } catch (\Throwable $th) {
            \Log::error($th);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }
    public function downloadPDFCardPinjaman(Request $request, $kodeAnggota)
    {
        try {
            // get anggota
            $anggota = Anggota::findOrFail($kodeAnggota);

            // get this year
            if (!$request->year) {
                $year = Carbon::today()->subYear()->endOfYear();
                $thisYear = Carbon::now()->year;
            } else {
                $year = Carbon::createFromFormat('Y', $request->year)->subYear()->endOfYear();
                $thisYear = Carbon::createFromFormat('Y', $request->year)->year;
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
                    $res['balance'] = $tabungan;
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

            view()->share('data', $data);
            PDF::setOptions(['margin-left' => 0, 'margin-right' => 0]);
            $pdf = PDF::loadView('pinjaman.card.detail', $data)->setPaper('a4', 'portrait');

            // download PDF file with download method
            $filename = 'export_kartu_pinjaman_' . Carbon::now()->format('d M Y') . '.pdf';
            return $pdf->download($filename);
        } catch (\Throwable $e) {
            \Log::error($e);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }
}
