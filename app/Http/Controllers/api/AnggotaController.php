<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Anggota;

class AnggotaController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->search;
        if($search == ''){
            $anggotas = Anggota::orderby('nama_anggota','asc')->select('kode_anggota','nama_anggota')->limit(5)->get();
        }else{
            $anggotas = Anggota::orderby('nama_anggota','asc')->select('kode_anggota','nama_anggota')->where('nama_anggota', 'like', '%' .$search . '%')->limit(5)->get();
        }
        $response = $anggotas->map(function ($anggota)
        {
            return [
                'id' => $anggota->kode_anggota,
                'text' => $anggota->nama_anggota
            ];
        });

        return response()->json($response,200);
    }
}
