<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Penarikan;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
  public function list(Request $request)
  {
      try
      {
          $user = $request->user('api');
          $anggota = $user->anggota;
          $list = ViewTransaksiSimpan::where('kode_anggota', $anggota->kode_anggota)->get();
          $data = $list->map(function ($transaksi) use ($request)
          {
              $user = $request->user('api');
              return [
                  'tglAju' => $transaksi->tgl_entri,
                  'jenis' => $transaksi->description,
                  'jumlah' => $transaksi->amount,
                  'status' => 'valid',
                  'tglTrf' => $transaksi->periode,
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
