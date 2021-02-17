<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PengajuanPinjamanController extends Controller
{
    public function store(Request $request)
    {
        try
        {
            $response = [];
            $rules = [
                'kode_anggota' => 'required',
                'id_jenis_pinjaman' => 'required',
                'id_jenis_penghasilan' => 'required',
                'besar_pinjaman' => 'required',
                'keperluan' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
            {
                $fields = array_keys($validator->errors()->toArray());
                $response = [
                    'message' => implode(', ', $fields). ' field are required'
                ];

                return response()->json($response, 404);
            }

            $user = $request->user('api');
            $anggota = Anggota::where('kode_anggota', $request->kode_anggota)
                                ->whereHas('user', function ($query) use ($user)
                                {
                                    return $query->where('id', $user->id);
                                })
                                ->first();

            if(is_null($anggota))
            {
                $response = [
                    'message' => 'Anggota not found'
                ];

                return response()->json($response, 404);
            }

            $data = [];
            $response = [
                'message' => null,
                'data' => $data
            ];
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
