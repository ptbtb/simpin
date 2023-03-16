<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Managers\AngsuranManager;
use App\Managers\PinjamanManager;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
class PinjamanController extends Controller
{
    public function Saldo(Request $request)
    {
        try
        {
            // $trans = DB::table('company_setting')
            //         ->where('name','saldo_pinjaman')
            //         ->first();
            //         if ($trans && $trans->value==1){
            //             $user = $request->user('api');
            // $anggota = $user->anggota;
            // $data['saldo'] = \App\Models\Pinjaman::where('kode_anggota', $anggota->kode_anggota)->sum('sisa_pinjaman');

            // $response['message'] = null;
            // $response['data'] = $data;
            // return response()->json($response, 200);
            //         }
            //  $response['message'] = 'Akan Segera Hadir';
            // return response()->json($response, 200);

            $user = $request->user('api');
            $anggota = $user->anggota;
            $tgl = Carbon::now()->format('Y-m-d');
            $data['saldo'] = PinjamanManager::getTotalPinjaman($anggota->kode_anggota)-AngsuranManager::getTotalAngsuran($anggota->kode_anggota);
//            $data['saldo'] = \App\Models\PinjamanV2::where('kode_anggota', $anggota->kode_anggota)->sum('sisa_pinjaman');
//            $data['saldo'] = 0;

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
