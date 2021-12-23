<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\TipeJurnal;
use App\Models\JurnalUmum;
use App\Models\Angsuran;
use App\Models\JurnalTemp;
use App\Models\Penarikan;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Exports\JurnalExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
// use Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use DB;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            if(!$request->from)
            {          
                $request->from = Carbon::today()->startOfMonth()->format('d-m-Y');
            }
            if(!$request->to)
            {          
                $request->to = Carbon::today()->endOfMonth()->format('d-m-Y');
            }

            $data['title'] = 'List Jurnal';
            $data['tipeJurnal'] = TipeJurnal::get()->pluck('name','id');
            $data['request'] = $request;
            return view('jurnal.index', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function indexAjax(Request $request)
    {

        try
        {
           $startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->format('Y-m-d');
           $endUntilPeriod = Carbon::createFromFormat   ('d-m-Y', $request->to)->format('Y-m-d');
           $jurnal = Jurnal::with('tipeJurnal','createdBy')->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
           if ($request->id_tipe_jurnal)
           {
            $jurnal = $jurnal->where('id_tipe_jurnal', $request->id_tipe_jurnal);
        }

        if ($request->serial_number)
        {
            $tipeJurnal = substr($request->serial_number,0,3);
            $year = substr($request->serial_number,3,4);
            $month = substr($request->serial_number,7,2);
            $serialNumber = (int)substr($request->serial_number,9,4);

            if($tipeJurnal == 'ANG')
            {
                $jurnalableType = 'App\Models\Angsuran';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_entri', '=', $year)->whereMonth('tgl_entri', '=', $month)->where('serial_number', $serialNumber);
                });
            }
            else if($tipeJurnal == 'MTS')
            {
                $jurnalableType = 'App\Models\JurnalTemp';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_posting', '=', $year)->whereMonth('tgl_posting', '=', $month)->where('serial_number', $serialNumber);
                });
            }
            else if($tipeJurnal == 'TRU')
            {
                $jurnalableType = 'App\Models\JurnalUmum';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                });
            }
            else if($tipeJurnal == 'TAR')
            {
                $jurnalableType = 'App\Models\Penarikan';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_ambil', '=', $year)->whereMonth('tgl_ambil', '=', $month)->where('serial_number', $serialNumber);
                });
            }
            else if($tipeJurnal == 'PIJ')
            {
                $jurnalableType = 'App\Models\Pinjaman';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_entri', '=', $year)->whereMonth('tgl_entri', '=', $month)->where('serial_number', $serialNumber);
                });
            }
            else if($tipeJurnal == 'SIP')
            {
                $jurnalableType = 'App\Models\Simpanan';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_entri', '=', $year)->whereMonth('tgl_entri', '=', $month)->where('serial_number', $serialNumber);
                });
            }
        }
        if($request->keterangan)
        {
            $jurnal = $jurnal->where('keterangan', 'like', '%' . $request->keterangan . '%');
        }
        if($request->code){
           $jurnal = $jurnal
           ->where(function ($query) use($request) {

             $query->where('akun_debet', 'like', '%' . $request->code . '%')
             ->orwhere('akun_kredit', 'like', '%' . $request->code . '%');
             
     });

       }




       $jurnal = $jurnal->orderBy('tgl_transaksi', 'desc');
       return DataTables::eloquent($jurnal)->addIndexColumn()->make(true);
   }
   catch (\Throwable $e)
   {
    $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
    Log::error($message);
    return response()->json(['message' => 'error'], 500);
}
}

public function createExcel(Request $request)
{
    try{
        if(!$request->from)
            {          
                $request->from = Carbon::today()->startOfMonth()->format('d-m-Y');
            }
            if(!$request->to)
            {          
                $request->to = Carbon::today()->endOfMonth()->format('d-m-Y');
            }
         $startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->format('Y-m-d');
           $endUntilPeriod = Carbon::createFromFormat   ('d-m-Y', $request->to)->format('Y-m-d');
        $jurnal = Jurnal::with('tipeJurnal','createdBy')->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);;
        if ($request->id_tipe_jurnal)
        {
            $jurnal = $jurnal->where('id_tipe_jurnal', $request->id_tipe_jurnal);
        }

        if ($request->serial_number)
        {
            $tipeJurnal = substr($request->serial_number,0,3);
            $year = substr($request->serial_number,3,4);
            $month = substr($request->serial_number,7,2);
            $serialNumber = (int)substr($request->serial_number,9,4);

            if($tipeJurnal == 'ANG')
            {
                $jurnalableType = 'App\Models\Angsuran';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_entri', '=', $year)->whereMonth('tgl_entri', '=', $month)->where('serial_number', $serialNumber);
                });
            }
            else if($tipeJurnal == 'MTS')
            {
                $jurnalableType = 'App\Models\JurnalTemp';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_posting', '=', $year)->whereMonth('tgl_posting', '=', $month)->where('serial_number', $serialNumber);
                });
            }
            else if($tipeJurnal == 'TRU')
            {
                $jurnalableType = 'App\Models\JurnalUmum';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                });
            }
            else if($tipeJurnal == 'TAR')
            {
                $jurnalableType = 'App\Models\Penarikan';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_ambil', '=', $year)->whereMonth('tgl_ambil', '=', $month)->where('serial_number', $serialNumber);
                });
            }
            else if($tipeJurnal == 'PIJ')
            {
                $jurnalableType = 'App\Models\Pinjaman';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_entri', '=', $year)->whereMonth('tgl_entri', '=', $month)->where('serial_number', $serialNumber);
                });
            }
            else if($tipeJurnal == 'SIP')
            {
                $jurnalableType = 'App\Models\Simpanan';

                $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class], function($query) use($year, $month, $serialNumber)
                {
                    $query->whereYear('tgl_entri', '=', $year)->whereMonth('tgl_entri', '=', $month)->where('serial_number', $serialNumber);
                });
            }
        }
        if($request->code){
           $jurnal = $jurnal
           ->where(function ($query) use($request) {

             $query->where('akun_debet', 'like', '%' . $request->code . '%')
             ->orwhere('akun_kredit', 'like', '%' . $request->code . '%');
             
     });

       }
       

       if($request->keterangan)
        {
            $jurnal = $jurnal->where('keterangan', 'like', '%' . $request->keterangan . '%');
        }

    $jurnal = $jurnal->orderBy('tgl_transaksi', 'desc')->get();
    $data['jurnal']= $jurnal;
    return (new FastExcel($jurnal))->download('export_jurnal_excel_' . Carbon::now()->format('d M Y') . '.xlsx',function($item){
         return [
        'Nomor' => $item->ser_num_view,
        'No Anggota' => $item->kode_anggota_view,
        'Tipe Jurnal' => ($item->tipeJurnal)?$item->tipeJurnal->name:'',
        'Akun Debet' => $item->akun_debet,
        'Debet' => (float)$item->debet,
        'Akun Kredit' => $item->akun_kredit,
        'Kredit' => (float)$item->kredit,
        'Keterangan' => $item->keterangan,
        'Tanggal' => $item->tgl_transaksi,
    ];
    });
    // $filename = 'export_jurnal_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
    // return Excel::download(new JurnalExport($data), $filename);
}
catch (\Throwable $e)
{
    $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
    Log::error($message);
    abort(500);
}
}
}