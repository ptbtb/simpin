<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Code;
use App\Models\KodeTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

use App\Exports\BukuBesarExport;
use App\Exports\BukuBesarResumeExport;
use Rap2hpoutre\FastExcel\FastExcel;

use Carbon\Carbon;
use Excel;
use DB;
use PDF;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            $jurnalCode = collect();
            $selisih = 0;
            if(!$request->period)
            {
                $request->period = Carbon::today()->format('Y-m-d');
            }

            if ($request->search)
            {
                $jurnalCode = KodeTransaksi::where('is_parent',0)
                                ->wherenotin('CODE',['606.01.000' , '606.01.101', '607.01.101'])
                                ->get();
                foreach ($jurnalCode as $key=>$jk){
                    $jurnalCode[$key]->amountnya=$jk->jurnalAmount($request->period);
                }

            }
//            dd($jurnalCode);
            $data['title'] = 'List Buku Besar';
            $data['codes'] = $jurnalCode;
            $data['request'] = $request;
            return view('buku_besar.index', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function createExcel(Request $request) {
        $user = Auth::user();
        $this->authorize('view jurnal', $user);
        if(!$request->period)
        {
            $request->period = Carbon::today()->format('Y-m-d');
        }

        $jurnalCode = KodeTransaksi::where('is_parent',0)
            ->wherenotin('CODE',['606.01.000' , '606.01.101', '607.01.101'])
            ->get();
        foreach ($jurnalCode as $key=>$jk){
            $jurnalCode[$key]->amountnya=$jk->jurnalAmount($request->period);
        }

        $data['title'] = 'List Buku Besar';
        $data['codes'] = $jurnalCode;
        $data['request'] = $request;
        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        // return (new FastExcel($bukuBesars))->download($filename);
        return Excel::download(new BukuBesarExport($data), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function createPdf(Request $request) {
        $user = Auth::user();
        $this->authorize('view jurnal', $user);
        if(!$request->period)
        {
            $request->period = Carbon::today()->format('Y-m-d');
        }


        $jurnalCode = KodeTransaksi::where('is_parent',0)
            ->wherenotin('CODE',['606.01.000' , '606.01.101', '607.01.101'])
            ->get();
        foreach ($jurnalCode as $key=>$jk){
            $jurnalCode[$key]->amountnya=$jk->jurnalAmount($request->period);
        }


        $data['title'] = 'List Buku Besar';
        $data['codes'] = $jurnalCode;
        $data['request'] = $request;
        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.pdf';
        // return (new FastExcel($bukuBesars))->download($filename);
        return Excel::download(new BukuBesarExport($data), $filename);
    }

    public function resume(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            $jurnalCode = collect();
            $selisih = 0;
            if(!$request->from)
            {
                $request->from = Carbon::today()->startOfMonth()->format('Y-m-d');
            }
            if(!$request->to)
            {
                $request->to = Carbon::today()->format('Y-m-d');
            }

//            if ($request->search)
//            {
//                $jurnalCode = KodeTransaksi::where('is_parent',0)
//                    ->wherenotin('CODE',['606.01.000' , '606.01.101', '607.01.101'])
//                    ->where('CODE','411.12.000' )
//                    ->get();
//                $tglawal =  Carbon::createFromFormat('Y-m-d',$request->from)->subDays(1)->format('Y-m-d');
////                dd($tglawal);
//                foreach ($jurnalCode as $key=>$jk){
//                    $jurnalCode[$key]->awaldr=$jk->jurnalAmount($tglawal)['dr'];
//                    $jurnalCode[$key]->awalcr=$jk->jurnalAmount($tglawal)['cr'];
//                    $jurnalCode[$key]->trxdr=$jk->jurnalAmountTransaksi($request->from,$request->to)['dr'];
//                    $jurnalCode[$key]->trxcr=$jk->jurnalAmountTransaksi($request->from,$request->to)['cr'];
//                    $jurnalCode[$key]->akhirdr=$jk->jurnalAmount($request->to)['dr'];
//                    $jurnalCode[$key]->akhircr=$jk->jurnalAmount($request->to)['cr'];
//                }
////                dd($jurnalCode);
//            }

            $data['title'] = 'Resume Buku Besar';
            $data['codes'] = $jurnalCode;
            $data['request'] = $request;
            return view('buku_besar.resume', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }

    public function resumeajax(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());

//            dd($request);
            $selisih = 0;
            if(!$request->from)
            {
                $request->from = Carbon::today()->startOfMonth()->format('Y-m-d');
            }
            if(!$request->to)
            {
                $request->to = Carbon::today()->format('Y-m-d');
            }

            if ($request->search)
            {
//                dd($request);
                $jurnalCode = KodeTransaksi::where('is_parent',0)
                    ->wherenotin('CODE',['606.01.000' , '606.01.101', '607.01.101'])
                    ->where('code_type_id',$request->code_type_id )
                    ->get();
                $tglawal =  Carbon::createFromFormat('Y-m-d',$request->from)->subDays(1)->format('Y-m-d');
//                dd($tglawal);
                foreach ($jurnalCode as $key=>$jk){
                    $jurnalCode[$key]->awaldr=number_format($jk->jurnalAmount($tglawal)['dr'],0,',','.');
                    $jurnalCode[$key]->awalcr=number_format($jk->jurnalAmount($tglawal)['cr'],0,',','.');
                    $jurnalCode[$key]->trxdr=number_format($jk->jurnalAmountTransaksi($request->from,$request->to)['dr'],0,',','.');
                    $jurnalCode[$key]->trxcr=number_format($jk->jurnalAmountTransaksi($request->from,$request->to)['cr'],0,',','.');
                    $jurnalCode[$key]->akhirdr=number_format($jk->jurnalAmount($request->to)['dr'],0,',','.');
                    $jurnalCode[$key]->akhircr=number_format($jk->jurnalAmount($request->to)['cr'],0,',','.');
                }
//                dd($jurnalCode);
            }

            return DataTables::of($jurnalCode)
                ->make(true);

    }

    public function resumeexcel(Request $request) {
        $jurnalCode = collect();
        $selisih = 0;
        if(!$request->from)
        {
            $request->from = Carbon::today()->startOfMonth()->format('Y-m-d');
        }
        if(!$request->to)
        {
            $request->to = Carbon::today()->format('Y-m-d');
        }


            $jurnalCode = KodeTransaksi::where('is_parent',0)
                ->wherenotin('CODE',['606.01.000' , '606.01.101', '607.01.101'])
                ->get();
            $tglawal =  Carbon::createFromFormat('Y-m-d',$request->from)->subDays(1)->format('Y-m-d');

        foreach ($jurnalCode as $key=>$jk){
            $jurnalCode[$key]->awaldr=$jk->jurnalAmount($tglawal)['dr'];
            $jurnalCode[$key]->awalcr=$jk->jurnalAmount($tglawal)['cr'];
            $jurnalCode[$key]->trxdr=$jk->jurnalAmountTransaksi($request->from,$request->to)['dr'];
            $jurnalCode[$key]->trxcr=$jk->jurnalAmountTransaksi($request->from,$request->to)['cr'];
            $jurnalCode[$key]->akhirdr=$jk->jurnalAmount($request->to)['dr'];
            $jurnalCode[$key]->akhircr=$jk->jurnalAmount($request->to)['cr'];
        }
//                dd($request);


        $data['title'] = 'List Buku Besar';
        $data['codes'] = $jurnalCode;
        $data['request'] = $request;
        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        // return (new FastExcel($bukuBesars))->download($filename);
        return Excel::download(new BukuBesarResumeExport($data), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
}
