<?php

namespace App\Http\Controllers;

use App\Models\JenisSimpanan;
use Illuminate\Http\Request;

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
}
