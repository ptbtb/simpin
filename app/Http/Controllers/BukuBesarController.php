<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

use App\Exports\BukuBesarExport;
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
            if(!$request->period)
            {
                $request->period = Carbon::today()->format('Y-m-d');
            }

            if ($request->search)
            {

                $codes = Code::where('is_parent', 0)->get();

                // buku besar collection
                $bukuBesars = collect();
                $todays=Carbon::createFromFormat('Y-m-d', $request->period);
                $today=Carbon::createFromFormat('Y-m-d', $request->period)->format('Y-m-d');

                $startOfNeraca =Carbon::createFromFormat('Y-m-d', '2020-12-31')->format('Y-m-d');
                $startOfLR = $todays->startOfYear()->format('Y-m-d');
                // / dd($startOfYear);

                foreach ($codes as $key => $code)
                {

//                    dd($code);
                    $saldo = 0;
                    // get code's normal balance
                    if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                    {
                        // if first char of COA is 7 or 8 get jurnal from first date of year until today
                        if($code->code_type_id==3 ||$code->code_type_id==4)
                        {


                            $saldoDebet = DB::table('buku_besar_v')
                                ->where('kode', $code->CODE)
                                ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                                ->where('trans','D')
                                ->sum('amount');
                            $saldoKredit = DB::table('buku_besar_v')
                                ->where('kode', $code->CODE)
                                ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                                ->where('trans','K')
                                ->sum('amount');

                        }
                        else
                        {
                            $saldoDebet = DB::table('buku_besar_v')
                                ->where('kode', $code->CODE)
                                ->whereBetween('tgl_transaksi', [$startOfNeraca,$today])
                                ->where('trans','D')
                                ->sum('amount');
                            $saldoKredit = DB::table('buku_besar_v')
                                ->where('kode', $code->CODE)
                                ->whereBetween('tgl_transaksi', [$startOfNeraca,$today])
                                ->where('trans','K')
                                ->sum('amount');

                        }

                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;

                        $bukuBesars->push([
                            'code' => $code->CODE,
                            'name' => $code->NAMA_TRANSAKSI,
                            'code_type_id' => $code->code_type_id,
                            'saldo' => $saldo,
                        ]);

                    }
                    else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                    {

                        // if first char of COA is 7 or 8 get jurnal from first date of year until today
                        if($code->code_type_id==3 ||$code->code_type_id==4)
                        {


                            $saldoDebet = DB::table('buku_besar_v')
                                ->where('kode', $code->CODE)
                                ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                                ->where('trans','D')
                                ->sum('amount');
                            $saldoKredit = DB::table('buku_besar_v')
                                ->where('kode', $code->CODE)
                                ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                                ->where('trans','K')
                                ->sum('amount');
                            $saldo -= $saldoDebet;
                            $saldo += $saldoKredit;
                            $bukuBesars->push([
                                'code' => $code->CODE,
                                'name' => $code->NAMA_TRANSAKSI,
                                'code_type_id' => $code->code_type_id,
                                'saldo' => $saldo,
                            ]);
                        }
                        else
                        {

                            $saldoDebet = DB::table('buku_besar_v')
                                ->where('kode', $code->CODE)
                                ->whereBetween('tgl_transaksi', [$startOfNeraca,$today])
                                ->where('trans','D')
                                ->sum('amount');
//                            if($code->codeCategory->name=='KEWAJIBAN LANCAR' &&  $code->codeType->name=='Passiva'){
//
//                                $saldoKredit =  DB::table('buku_besar_v')
//                                    ->where('kode', $code->CODE)
//                                    ->whereBetween('tgl_transaksi', [$startOfLR,$today])
//                                    ->where('trans','K')
//                                    ->sum('amount');
//                                $saldo += $saldoDebet;
//                                $saldo -= $saldoKredit;
//                                $bukuBesars->push([
//                                    'code' => $code->CODE,
//                                    'name' => $code->NAMA_TRANSAKSI,
//                                    'code_type_id' => $code->code_type_id,
//                                    'saldo' => -1*$saldo,
//                                ]);
//                            }
//                            else
                                if($code->codeCategory->name=='AKTIVA TETAP' &&  $code->codeType->name=='Activa')

                            {
                                $saldoKreditJurnalUmum = Jurnal::where('akun_kredit', $code->CODE)->whereIn('jurnalable_type', ['App\Models\JurnalUmum','App\Models\JurnalTemp'])->where('tgl_transaksi', '<=',$today)->sum('kredit');
                                $saldoKreditSaldoAwal = Jurnal::where('akun_kredit', $code->CODE)->where('jurnalable_type', 'App\Models\SaldoAwal')->where('tgl_transaksi', '<=',$today)->sum('kredit');
                                $saldoKredit = $saldoKreditSaldoAwal + (-1 * $saldoKreditJurnalUmum);


                                $saldo += $saldoDebet;
                                $saldo -= $saldoKredit;

                                $bukuBesars->push([
                                    'code' => $code->CODE,
                                    'name' => $code->NAMA_TRANSAKSI,
                                    'code_type_id' => $code->code_type_id,
                                    'saldo' => -1*$saldo,
                                ]);
                            }
                            else
                            {
                                $saldoKredit = DB::table('buku_besar_v')
                                    ->where('kode', $code->CODE)
                                    ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                                    ->where('trans','K')
                                    ->sum('amount');
                                $saldo -= $saldoDebet;
                                $saldo += $saldoKredit;
                                $bukuBesars->push([
                                    'code' => $code->CODE,
                                    'name' => $code->NAMA_TRANSAKSI,
                                    'code_type_id' => $code->code_type_id,
                                    'saldo' => $saldo,
                                ]);
                            }
                        }




                    }
                    //dd($bukuBesars);
                }

                $bukuBesars = $bukuBesars->sortBy('code');

                $data['codes'] = $codes;
                $data['bukuBesars'] = $bukuBesars;
            }

            $data['title'] = 'List Buku Besar';
            $data['request'] = $request;
            //dd($data['request'] );
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
        $codes = Code::where('is_parent', 0)->get();

        //$jurnal = Jurnal::get();

        // buku besar collection
        $bukuBesars = collect();
        if(!$request->period)
        {
            $request->period = Carbon::today()->format('Y-m-d');
        }

        $todays=Carbon::createFromFormat('Y-m-d', $request->period);
        $today=Carbon::createFromFormat('Y-m-d', $request->period)->format('Y-m-d');

        $startOfNeraca =Carbon::createFromFormat('Y-m-d', '2020-12-31')->format('Y-m-d');
        $startOfLR = $todays->startOfYear()->format('Y-m-d');
        // / dd($startOfYear);

        foreach ($codes as $key => $code)
        {

//                    dd($code);
            $saldo = 0;
            // get code's normal balance
            if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
            {
                // if first char of COA is 7 or 8 get jurnal from first date of year until today
                if($code->code_type_id==3 ||$code->code_type_id==4)
                {


                    $saldoDebet = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                        ->where('trans','D')
                        ->sum('amount');
                    $saldoKredit = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                        ->where('trans','K')
                        ->sum('amount');

                }
                else
                {
                    $saldoDebet = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfNeraca,$today])
                        ->where('trans','D')
                        ->sum('amount');
                    $saldoKredit = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfNeraca,$today])
                        ->where('trans','K')
                        ->sum('amount');

                }

                $saldo += $saldoDebet;
                $saldo -= $saldoKredit;

                $bukuBesars->push([
                    'code' => $code->CODE,
                    'name' => $code->NAMA_TRANSAKSI,
                    'type' => $code->codeType->name,
                    'saldo' => $saldo,
                ]);

            }
            else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
            {

                // if first char of COA is 7 or 8 get jurnal from first date of year until today
                if($code->code_type_id==3 ||$code->code_type_id==4)
                {


                    $saldoDebet = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                        ->where('trans','D')
                        ->sum('amount');
                    $saldoKredit = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                        ->where('trans','K')
                        ->sum('amount');
                    $saldo -= $saldoDebet;
                    $saldo += $saldoKredit;
                    $bukuBesars->push([
                        'code' => $code->CODE,
                        'name' => $code->NAMA_TRANSAKSI,
                        'type' => $code->codeType->name,
                        'saldo' => $saldo,
                    ]);
                }
                else
                {

                    $saldoDebet = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfNeraca,$today])
                        ->where('trans','D')
                        ->sum('amount');
                    if($code->codeCategory->name=='KEWAJIBAN LANCAR' &&  $code->codeType->name=='Passiva'){

                        $saldoKredit =  DB::table('buku_besar_v')
                            ->where('kode', $code->CODE)
                            ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                            ->where('trans','K')
                            ->sum('amount');
                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;
                        $bukuBesars->push([
                            'code' => $code->CODE,
                            'name' => $code->NAMA_TRANSAKSI,
                            'type' => $code->codeType->name,
                            'saldo' => -1*$saldo,
                        ]);
                    }
                    else if($code->codeCategory->name=='AKTIVA TETAP' &&  $code->codeType->name=='Activa')

                    {
                        $saldoKreditJurnalUmum = Jurnal::where('akun_kredit', $code->CODE)->whereIn('jurnalable_type', ['App\Models\JurnalUmum','App\Models\JurnalTemp'])->where('tgl_transaksi', '<=',$today)->sum('kredit');
                        $saldoKreditSaldoAwal = Jurnal::where('akun_kredit', $code->CODE)->where('jurnalable_type', 'App\Models\SaldoAwal')->where('tgl_transaksi', '<=',$today)->sum('kredit');
                        $saldoKredit = $saldoKreditSaldoAwal + (-1 * $saldoKreditJurnalUmum);


                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;

                        $bukuBesars->push([
                            'code' => $code->CODE,
                            'name' => $code->NAMA_TRANSAKSI,
                            'type' => $code->codeType->name,
                            'saldo' => -1*$saldo,
                        ]);
                    }
                    else
                    {
                        $saldoKredit = DB::table('buku_besar_v')
                            ->where('kode', $code->CODE)
                            ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                            ->where('trans','K')
                            ->sum('amount');
                        $saldo -= $saldoDebet;
                        $saldo += $saldoKredit;
                        $bukuBesars->push([
                            'code' => $code->CODE,
                            'name' => $code->NAMA_TRANSAKSI,
                            'type' => $code->codeType->name,
                            'saldo' => $saldo,
                        ]);
                    }
                }




            }
            //dd($bukuBesars);
        }

        $bukuBesars = $bukuBesars->sortBy('code');
        $data['bukuBesars'] = $bukuBesars->sortBy('code');
        $data['request'] = $request;
        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        // return (new FastExcel($bukuBesars))->download($filename);
        return Excel::download(new BukuBesarExport($data), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function createPdf(Request $request) {
        $user = Auth::user();
        $this->authorize('view jurnal', $user);
        $codes = Code::where('is_parent', 0)->get();

        //$jurnal = Jurnal::get();

        // buku besar collection
        $bukuBesars = collect();
        if(!$request->period)
        {
            $request->period = Carbon::today()->format('Y-m-d');
        }

        $todays=Carbon::createFromFormat('Y-m-d', $request->period);
        $today=Carbon::createFromFormat('Y-m-d', $request->period)->format('Y-m-d');

        $startOfNeraca =Carbon::createFromFormat('Y-m-d', '2020-12-31')->format('Y-m-d');
        $startOfLR = $todays->startOfYear()->format('Y-m-d');
        // / dd($startOfYear);

        foreach ($codes as $key => $code)
        {

//                    dd($code);
            $saldo = 0;
            // get code's normal balance
            if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
            {
                // if first char of COA is 7 or 8 get jurnal from first date of year until today
                if($code->code_type_id==3 ||$code->code_type_id==4)
                {


                    $saldoDebet = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                        ->where('trans','D')
                        ->sum('amount');
                    $saldoKredit = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                        ->where('trans','K')
                        ->sum('amount');

                }
                else
                {
                    $saldoDebet = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfNeraca,$today])
                        ->where('trans','D')
                        ->sum('amount');
                    $saldoKredit = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfNeraca,$today])
                        ->where('trans','K')
                        ->sum('amount');

                }

                $saldo += $saldoDebet;
                $saldo -= $saldoKredit;

                $bukuBesars->push([
                    'code' => $code->CODE,
                    'name' => $code->NAMA_TRANSAKSI,
                    'type' => $code->codeType->name,
                    'saldo' => $saldo,
                ]);

            }
            else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
            {

                // if first char of COA is 7 or 8 get jurnal from first date of year until today
                if($code->code_type_id==3 ||$code->code_type_id==4)
                {


                    $saldoDebet = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                        ->where('trans','D')
                        ->sum('amount');
                    $saldoKredit = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                        ->where('trans','K')
                        ->sum('amount');
                    $saldo -= $saldoDebet;
                    $saldo += $saldoKredit;
                    $bukuBesars->push([
                        'code' => $code->CODE,
                        'name' => $code->NAMA_TRANSAKSI,
                        'type' => $code->codeType->name,
                        'saldo' => $saldo,
                    ]);
                }
                else
                {

                    $saldoDebet = DB::table('buku_besar_v')
                        ->where('kode', $code->CODE)
                        ->whereBetween('tgl_transaksi', [$startOfNeraca,$today])
                        ->where('trans','D')
                        ->sum('amount');
                    if($code->codeCategory->name=='KEWAJIBAN LANCAR' &&  $code->codeType->name=='Passiva'){

                        $saldoKredit =  DB::table('buku_besar_v')
                            ->where('kode', $code->CODE)
                            ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                            ->where('trans','K')
                            ->sum('amount');
                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;
                        $bukuBesars->push([
                            'code' => $code->CODE,
                            'name' => $code->NAMA_TRANSAKSI,
                            'type' => $code->codeType->name,
                            'saldo' => -1*$saldo,
                        ]);
                    }
                    else if($code->codeCategory->name=='AKTIVA TETAP' &&  $code->codeType->name=='Activa')

                    {
                        $saldoKreditJurnalUmum = Jurnal::where('akun_kredit', $code->CODE)->whereIn('jurnalable_type', ['App\Models\JurnalUmum','App\Models\JurnalTemp'])->where('tgl_transaksi', '<=',$today)->sum('kredit');
                        $saldoKreditSaldoAwal = Jurnal::where('akun_kredit', $code->CODE)->where('jurnalable_type', 'App\Models\SaldoAwal')->where('tgl_transaksi', '<=',$today)->sum('kredit');
                        $saldoKredit = $saldoKreditSaldoAwal + (-1 * $saldoKreditJurnalUmum);


                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;

                        $bukuBesars->push([
                            'code' => $code->CODE,
                            'name' => $code->NAMA_TRANSAKSI,
                            'type' => $code->codeType->name,
                            'saldo' => -1*$saldo,
                        ]);
                    }
                    else
                    {
                        $saldoKredit = DB::table('buku_besar_v')
                            ->where('kode', $code->CODE)
                            ->whereBetween('tgl_transaksi', [$startOfLR,$today])
                            ->where('trans','K')
                            ->sum('amount');
                        $saldo -= $saldoDebet;
                        $saldo += $saldoKredit;
                        $bukuBesars->push([
                            'code' => $code->CODE,
                            'name' => $code->NAMA_TRANSAKSI,
                            'type' => $code->codeType->name,
                            'saldo' => $saldo,
                        ]);
                    }
                }




            }
            //dd($bukuBesars);
        }

        $bukuBesars = $bukuBesars->sortBy('code');
        $data['bukuBesars'] = $bukuBesars->sortBy('code');
        $data['request'] = $request;
        $filename = 'export_buku_besar_excel_' . Carbon::now()->format('d M Y') . '.pdf';
        // return (new FastExcel($bukuBesars))->download($filename);
        return Excel::download(new BukuBesarExport($data), $filename);
    }
}
