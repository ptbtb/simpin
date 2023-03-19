<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Managers\PenarikanManager;
use App\Managers\SimpananManager;
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
            $data['saldo'] = SimpananManager::getTotalSimpanan($anggota->kode_anggota)-PenarikanManager::getTotalPenarikan($anggota->kode_anggota);
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
            if (!$request->year) {
                $year = Carbon::today()->subYear()->endOfYear();
                $thisYear = Carbon::now()->year;
            } else {
                $year = Carbon::createFromFormat('Y', $request->year)->subYear()->endOfYear();
                $thisYear = Carbon::createFromFormat('Y', $request->year)->year;
            }
            $from = Carbon::createFromFormat('Y',$thisYear)->startOfYear()->format('Y-m-d');
            $to = Carbon::createFromFormat('Y',$thisYear)->endOfYear()->format('Y-m-d');

           
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
            $listPengambilan = PenarikanManager::getListPenarikan($anggota->kode_anggota,$from,$to)
                ->get();


            $listSimpanan = [];
            $index = count($requiredKey);
            foreach ($groupedListSimpanan as $key => $list) {
                $jenisSimpanan = JenisSimpanan::find($key);
                if ($jenisSimpanan) {
                    $tabungan = $awalSimpan->where('akun_kredit',$key)->sum('kredit')-$awaltarik->where('akun_debet',$key)->sum('debet');
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
            // dd($data);
            // share data to view
            view()->share('data', $data);
            PDF::setOptions(['margin-left' => 0, 'margin-right' => 0]);
            $pdf = PDF::loadView('simpanan.card.detail', $data)->setPaper('a4', 'portrait');

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

    public function YearList(Request $request)
    {
        try
        {
            $user = $request->user('api');
            $anggota = $user->anggota;
            $listtahun = [
            Carbon::now()->format('Y'),
            Carbon::now()->subYear()->format('Y'),
        ];

            $response['message'] = null;
            $response['data'] = $listtahun;
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


}
