<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\Simpanan;
use Carbon\Carbon;
use PDF;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\View\ViewSaldo;

use Illuminate\Support\Facades\Log;
class SimpananController extends Controller
{
    public function Saldo(Request $request)
    {
        try
        {
            $user = $request->user('api');
            $anggota = $user->anggota;
            $data['saldo'] = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->sum('jumlah');
            $response['message'] = null;
            $response['data'] = $data;
            return response()->json($response, 200);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            $response['message'] = API_DEFAULT_ERROR_MESSAGE;
            return response()->json($response, 500);
        }
    }

    public function Detail(Request $request)
    {
        try
        {
            $user = $request->user('api');
            $anggota = $user->anggota;
            $data['list'] = \App\Models\Simpanan::where('kode_anggota', $anggota->kode_anggota)->get();

            $response['message'] = null;
            $response['data'] = $data;
            return response()->json($response, 200);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            $response['message'] = API_DEFAULT_ERROR_MESSAGE;
            return response()->json($response, 500);
        }
    }
    public function showCard(Request $request)
    {
        try
        {
            // get anggota
            $user = $request->user('api');
            $anggota = $user->anggota;
            $year= Carbon::today()->subYear()->endOfYear();

            // get this year
            $thisYear = Carbon::now()->year;
            $listTabungan = \App\Models\View\ViewSimpanSaldoAwal::where('kode_anggota', $anggota->kode_anggota)
                ->get();
            // get list simpanan by this year and kode anggota. sort by tgl_entry ascending
            $listSimpanan = Simpanan::whereYear('tgl_transaksi', $thisYear)
                ->where('kode_anggota', $anggota->kode_anggota)
                ->where("mutasi",0)
                ->orderBy('periode', 'asc')
                ->get();
            // data di grouping berdasarkan kode jenis simpan
            $groupedListSimpanan = $listSimpanan->groupBy('kode_jenis_simpan');

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
            $listPengambilan = Penarikan::where('kode_anggota', $anggota->kode_anggota)
                ->whereYear('tgl_transaksi', $thisYear)
                ->whereIn('code_trans', $simpananKeys)
                ->whereraw('paid_by_cashier is not null')
                ->orderBy('tgl_transaksi', 'asc')
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
                    $tabungan = $anggota->simpanSaldoAwal->where('kode_trans', $key)->first();
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
                    $res['balance'] = ($tabungan) ? $tabungan->besar_tabungan+$transsimpan-$transtarik : 0;
                    $res['list'] = $list;
                    $res['amount'] = $list->sum('besar_simpanan');
                    $res['final_balance'] = $res['balance'] + $res['amount'];
                    $res['withdrawalList'] = $listPengambilan->where('code_trans', $key)->values();
                    $res['withdrawalAmount'] = $listPengambilan->where('code_trans', $key)->values()->sum('besar_ambil');
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
            $pdf = PDF::loadView('simpanan.card.export2', $data)->setPaper('a4', 'portrait');

            // download PDF file with download method
            $filename = 'export_kartu_simpanan_' . Carbon::now()->format('d M Y') . '.pdf';
            return $pdf->download($filename);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            $response['message'] = $message;//API_DEFAULT_ERROR_MESSAGE;
            return response()->json($response, 500);
        }
    }


}
