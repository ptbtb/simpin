<?php

namespace App\Http\Controllers;

use App\Models\JenisSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JenisSimpananController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->search;
        if($search == ''){
            $listJenisSimpanan = JenisSimpanan::orderby('nama_simpanan','asc')->select('kode_jenis_simpan','nama_simpanan')->limit(10)->get();
        }else{
            $listJenisSimpanan = JenisSimpanan::orderby('nama_simpanan','asc')->select('kode_jenis_simpan','nama_simpanan')->where('kode_jenis_simpan', $search)->limit(10)->get();
        }
        $response = $listJenisSimpanan->map(function ($jenisSimpanan)
        {
            return [
                'id' => $jenisSimpanan->kode_jenis_simpan,
                'text' => strtoupper($jenisSimpanan->nama_simpanan)
            ];
        });

        return response()->json($response,200);
    }

    public function searchId($id)
    {
        return JenisSimpanan::find($id);
    }

    public function searchSimpananByUser(Request $request){
        $user = $request->userId;

        $checkSimpananPokokAnggota = DB::table('t_simpan')
                                ->join('t_jenis_simpan', 't_jenis_simpan.kode_jenis_simpan', 't_simpan.kode_jenis_simpan')
                                ->join('t_anggota', 't_anggota.kode_anggota', 't_simpan.kode_anggota')
                                ->where('t_simpan.kode_jenis_simpan', '=', '411.01.000')
                                ->where('t_simpan.besar_simpanan', '>=', 500000)
                                ->where('t_simpan.kode_anggota', '=', $user)
                                ->first();
        
        if (is_null($checkSimpananPokokAnggota)){
            $listJenisSimpanan = JenisSimpanan::orderby('nama_simpanan','asc')->select('kode_jenis_simpan','nama_simpanan')->where('nama_simpanan', '!=', 'SIMPANAN KHUSUS PAGU')->limit(10)->get();
        }
        else {
            $listJenisSimpanan = JenisSimpanan::where('nama_simpanan', '!=', 'SIMPANAN POKOK')->where('nama_simpanan', '!=', 'SIMPANAN KHUSUS PAGU')->get();
        }
        
        $response = $listJenisSimpanan->map(function ($jenisSimpanan)
        {
            return [
                'id' => $jenisSimpanan->kode_jenis_simpan,
                'text' => strtoupper($jenisSimpanan->nama_simpanan)
            ];
        });

        return response()->json($response, 200);
    }
}
