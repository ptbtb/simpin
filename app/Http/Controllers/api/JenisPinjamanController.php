<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JenisPinjamanController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $listJenisPinjaman = JenisPinjaman::get();
            $data = $listJenisPinjaman->map(function ($jenisPinjaman) use ($request)
            {
                $maxPinjaman = 0;
                $user = $request->user('api');
                if ($user)
                {
                    $maxPinjaman = $jenisPinjaman->maxPinjaman($user);
                }
                return [
                    'kode' => $jenisPinjaman->kode_jenis_pinjam,
                    'nama' => $jenisPinjaman->nama_pinjaman,
                    'lama_angsuran' => $jenisPinjaman->lama_angsuran,
                    'max_pinjaman' => $maxPinjaman
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
