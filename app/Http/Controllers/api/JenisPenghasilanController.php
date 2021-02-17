<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisPenghasilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JenisPenghasilanController extends Controller
{
    public function index()
    {
        try
        {
            $listJenisPenghasilan = JenisPenghasilan::get();
            $data = $listJenisPenghasilan->map(function ($jenisPenghasilan)
            {
                return [
                    'id' => $jenisPenghasilan->id,
                    'nama' => $jenisPenghasilan->name,
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
