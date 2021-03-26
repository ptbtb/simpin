<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
class PinjamanController extends Controller
{
    public function Saldo(Request $request)
    {
        try
        {
            $user = $request->user('api');
            $anggota = $user->anggota;
            $data['saldo'] = \App\Models\Pinjaman::where('kode_anggota', $anggota->kode_anggota)->sum('sisa_pinjaman');

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
            $data['List'] = $listPinjaman = \App\Models\Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                        ->notPaid()
                        ->get();

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


}
