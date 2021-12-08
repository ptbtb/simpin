<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JenisSimpananController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $list = JenisSimpanan::get();
            $data = $list->map(function ($jenisSimpanan) use ($request)
            {
                $user = $request->user('api');
                return [
                    'kode' => $jenisSimpanan->kode_jenis_simpan,
                    'nama' => $jenisSimpanan->nama_simpanan,
                ];
            });

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
