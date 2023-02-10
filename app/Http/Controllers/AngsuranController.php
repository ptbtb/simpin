<?php

namespace App\Http\Controllers;

use App\Imports\AngsuranImport;
use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\AngsuranPartial;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Database\Eloquent\ModelNotfoundException;
use Illuminate\Support\Facades\Auth;

class AngsuranController extends Controller
{
    public function importAngsuran()
    {
        try
        {
            $data['title'] = 'Import Angsuran';
            return view('pinjaman.angsuran.import', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function storeImportAngsuran(Request $request)
    {
        try
        {
            DB::transaction(function () use ($request)
            {
                // Excel::import(new AngsuranImport, $request->file);
                $collection = (new FastExcel)->import($request->file);
                foreach ($collection as $transaksi) {
                    AngsuranImport::generatetransaksi($transaksi);
                }
            });

            return redirect()->back()->withSuccess('Berhasil Import Data');
        }
        catch (\Throwable $e)
        {
            $message = $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError($message);
        }
    }

    public function jurnalShow($id)
    {
      try {
        $jurnals= collect();
        $angsuran = Angsuran::findOrFail($id);
        if($angsuran->jurnals->count()>0){
          // $jurnals->push($angsuran->jurnals);
          // $angsuran->put('jurnal',$angsuran->jurnals);
          if($angsuran->jurnals){
            foreach ($angsuran->jurnals as $jurn) {
              // code...
                $jurnals->push($jurn);
            }
          }
        }else{
          if($angsuran->angsuranPartial->count()>0){
            foreach ($angsuran->angsuranPartial as  $partial) {
              // dd($partial->jurnals);
              if($partial->jurnals){
                foreach ($partial->jurnals as $jurn) {
                  // code...
                    $jurnals->push($jurn);
                }
              }

            }
          }
        }
        $data['jurnals'] = $jurnals;
        return view('pinjaman.jurnal', $data);
      } catch (\Exception $e) {

      }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->roles->first();
        $this->authorize('view angsuran', $user);
        $kodepinjam = Pinjaman::pluck('kode_pinjam')->all();
        // check role user
        if ($user->roles->first()->id == ROLE_ANGGOTA) {
            $anggota = $user->anggota;
            if (is_null($anggota)) {
                return redirect()->back()->withError('Your account has no members');
            }

            $listAngsuran = Angsuran::where('kode_anggota', $anggota->kode_anggota)
                ->wherenotnull('tgl_transaksi')
                ->orderBy('tgl_entri', 'asc');
        } else {
            if ($request->id) {
                $anggota = Anggota::find($request->id);

                $listAngsuran = Angsuran::where('kode_anggota', $anggota->kode_anggota)
                    ->wherenotnull('tgl_transaksi')
                    ->orderBy('tgl_entri', 'asc');;
            } else {
                $listAngsuran = Angsuran::wherenotnull('tgl_transaksi')
                    ->orderby('created_at', 'desc');
            }
        }

        if (!$request->from) {
            if ($request->id) {
                $request->from = Carbon::createFromFormat('Y-m-d', '2021-01-01')->format('Y-m-d');
            } else {
                $request->from = Carbon::now()->startOfDay()->format('Y-m-d');
            }
        } else {
            $request->from = Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay()->format('Y-m-d');
        }

        if (!$request->to) {
            $request->to = Carbon::now()->endOfDay()->format('Y-m-d');
        } else {
            $request->to = Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay()->format('Y-m-d');
        }
        if ($request->kode_pinjam) {
            $listAngsuran = $listAngsuran->where('kode_pinjam', $request->kode_pinjam);
        }
        $listAngsuran = $listAngsuran->whereBetween('tgl_entri', [$request->from, $request->to])->get();
        $data['title'] = "List Angsuran";
        $data['listAngsuran'] = $listAngsuran;
        $data['request'] = $request;
        $data['kodepinjam'] = $kodepinjam;
        return view('pinjaman.indexAngsuran', $data);
    }
}
