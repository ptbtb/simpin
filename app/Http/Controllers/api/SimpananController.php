<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
class SimpananController extends Controller
{
    public function Saldo(Request $request)
    {
        try
        {
            $user = $request->user('api');
            $anggota = $user->anggota;
            $data['saldo'] = \App\Models\Simpanan::where('kode_anggota', $anggota->kode_anggota)->sum('besar_simpanan');

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


            // get this year
            $thisYear = Carbon::now()->year;
            // $thisYear = 2020;

            // get list simpanan by this year and kode anggota. sort by tgl_entry ascending
            $listSimpanan = Simpanan::whereYear('tgl_entri', $thisYear)
                ->where('kode_anggota', $anggota->kode_anggota)
                ->whereraw("keterangan not like '%MUTASI%'")
                ->orderBy('tgl_entri','asc')
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
                    $res['name'] = $jenisSimpanan->nama_simpanan;
                    $res['balance'] = ($tabungan)? $tabungan->besar_tabungan:0;
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
            $response['message'] = null;
            $response['data'] = $data;
            return response()->json($response, 200);
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
