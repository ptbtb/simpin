<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\Simpanan;
use App\Models\Tabungan;
use App\Models\Code;
use App\Models\JkkPrinted;
use App\Models\Pinjaman;
use App\Models\SimpinRule;
use App\Models\View\ViewSaldo;
use App\Models\StatusPenarikan;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class PenarikanController extends Controller
{
    public function list(Request $request)
    {
        try
        {
            $user = $request->user('api');
            $anggota = $user->anggota;
            $list = Penarikan::where('kode_anggota', $anggota->kode_anggota)->get();
            $data = $list->map(function ($penarikan) use ($request)
            {
                $user = $request->user('api');
                return [
                    'tglAju' => $penarikan->tgl_ambil,
                    'jenis' => $penarikan->jenisSimpanan->nama_simpanan,
                    'jumlah' => $penarikan->besar_ambil,
                    'status' => $penarikan->statusPenarikan->name,
                    'tglTrf' => $penarikan->tgl_transaksi,
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


    public function ajuAmbil(Request $request)
    {
        try
        {
            $user = $request->user('api');
            $anggota = $user->anggota;
           $check = Hash::check($request->password, $user->password);
            if (!$check) {
                 $response = [
                    'message' => 'Password yang anda masukkan salah'
                ];

                return response()->json($response, 403);
            }

             // check max penarikan user
            $thisYear = Carbon::now()->year;
            $penarikanUser = Penarikan::approved()
                                        ->where('kode_anggota', $anggota->kode_anggota)
                                        ->whereYear('created_at', $thisYear)
                                        ->get();

            $simpinRule = SimpinRule::findOrFail(SIMPIN_RULE_MAX_PENGAMBILAN_DALAM_SETAHUN);

            if ($penarikanUser->count() >= $simpinRule->value)
            {
                 $response = [
                    'message' => 'Gagal melakukan penarikan. Jumlah penarikan anda tahun ini adalah '. $penarikanUser->count() .'.Maksimal penarikan dalam setahun adalah '. $simpinRule->value
                ];

                return response()->json($response, 403);
            }

            $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                                    ->notPaid()
                                    ->japan()
                                    ->get();

            $tenor1 = $listPinjaman->whereIn('lama_angsuran', [36,48,60,72])
                                    ->values();
            $tenor2 = $listPinjaman->whereIn('lama_angsuran', [10, 20, 30])
                                    ->values();

            foreach ($request->jenis_simpanan as $kode)
            {
                $jenissimpanan = JenisSimpanan::where('kode_jenis_simpan', $kode)->first();
                $tabungan = $anggota->tabungan->where('kode_trans', $kode)->first();
                $besarPenarikan = filter_var($request->besar_penarikan[$kode], FILTER_SANITIZE_NUMBER_INT);
                $maxtarik = $tabungan->totalBesarTabungan * $jenissimpanan->max_withdraw;
                
                if (is_null($tabungan))
                {
                    
                     $response = [
                    'message' => $anggota->nama_anggota . " belum memiliki tabungan"
                ];

                return response()->json($response, 403);
                }
                else if ($tabungan->totalBesarTabungan < $besarPenarikan)
                {
                    
                     $response = [
                    'message' => "Saldo tabungan tidak mencukupi"
                ];

                return response()->json($response, 403);
                }
                else if($tenor1->count())
                {
                    $sisaPinjaman = $tenor1->sum('sisa_pinjaman');
                    $minSaldo = 1/5*$sisaPinjaman;
                    if ($tabungan->besar_tabungan < $minSaldo)
                    {
                         $response = [
                    'message' => "Saldo tabungan tidak mencukupi. Minimal saldo yang tersisa harus lebih dari Rp ". number_format($minSaldo, 0, ',', '.')
                ];

                return response()->json($response, 403);
                    }
                }
                else if($tenor2->count())
                {
                    $sisaPinjaman = $tenor2->sum('sisa_pinjaman');
                    $minSaldo = 1/8*$sisaPinjaman;
                    if ($tabungan->besar_tabungan < $minSaldo)
                    {
                        
                         $response = [
                    'message' => "Saldo tabungan tidak mencukupi. Minimal saldo yang tersisa harus lebih dari Rp ". number_format($minSaldo, 0, ',', '.')
                ];

                return response()->json($response, 403);
                    }
                }
                else if ($besarPenarikan > $maxtarik + 1)
                {
                   
                     $response = [
                    'message' => "Penarikan simpanan " . $jenissimpanan->nama_simpanan . " tidak boleh melebihi ".$jenissimpanan->max_withdraw." dari saldo tabungan"
                ];

                return response()->json($response, 403);
                }
            }

            foreach ($request->jenis_simpanan as $kode)
            {
                $penarikan = new Penarikan();
                // get next serial number
                $nextSerialNumber = PenarikanManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
                $tabungan = $anggota->tabungan->where('kode_trans', $kode)->first();
                $besarPenarikan = filter_var($request->besar_penarikan[$kode], FILTER_SANITIZE_NUMBER_INT);

                DB::transaction(function () use ($besarPenarikan, $anggota, $tabungan, &$penarikan, $user, $nextSerialNumber) {
                    $penarikan->kode_anggota = $anggota->kode_anggota;
                    $penarikan->kode_tabungan = $tabungan->kode_tabungan;
                    $penarikan->id_tabungan = $tabungan->id;
                    $penarikan->besar_ambil = $besarPenarikan;
                    $penarikan->code_trans = $tabungan->kode_trans;
                    $penarikan->tgl_ambil = Carbon::now();
                    $penarikan->u_entry = $user->name;
                    $penarikan->created_by = $user->id;
                    $penarikan->status_pengambilan = STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI;
                    $penarikan->serial_number = $nextSerialNumber;
                    $penarikan->save();
                });

                event(new PenarikanCreated($penarikan, $tabungan));
            }
            // return redirect()->route('penarikan-receipt', ['id' => $penarikan->kode_ambil])->withSuccess("Penarikan berhasil");
            
            $response['message'] = 'Permintaan penarikan berhasil disimpan dan dalam proses persetujuan';
            return response()->json($response, 200);

        } catch (\Exception $e) {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            $response['message'] = API_DEFAULT_ERROR_MESSAGE;
            return response()->json($response, 500);
        }
    }

   

}
