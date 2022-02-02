<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\JenisSimpanan;
use App\Models\JkkPrinted;
use App\Models\JkkPrintedType;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

use Storage;

class JkkPrintedController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $this->authorize('print jkk', $user);
        // dd($request);
        $types = JkkPrintedType::all();
        if(!$request->from)
            {
                $request->from = Carbon::today()->startOfMonth()->format('d-m-Y');
            }
            if(!$request->to)
            {
                $request->to = Carbon::today()->endOfMonth()->format('d-m-Y');
            }
        $data['types'] = $types;
        $data['title'] = 'Jkk Printed';
        $data['request'] = $request;
        return view('jkk.printed', $data);
    }

    public function indexAjax(Request $request)
    {
        $user = Auth::user();
        $this->authorize('print jkk', $user);
          $startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->startOfDay()->format('Y-m-d');
          $endUntilPeriod = Carbon::createFromFormat   ('d-m-Y', $request->to)->endOfDay()->format('Y-m-d');

        $jkkPrinted = JkkPrinted::with('jkkPrintedType', 'jkkPengajuan', 'jkkPenarikan')
                                ->orderBy('printed_at', 'desc');
        if ($request->no_jkk){
            $jkkPrinted = $jkkPrinted->where('jkk_number',$request->no_jkk);
        }


        if (isset($request->type_id) && $request->type_id == JKK_PRINTED_TYPE_PENGAJUAN_PINJAMAN)
        {
            $jkkPrinted = $jkkPrinted->whereHas('jkkPengajuan', function ($query) use ($startUntilPeriod,&$endUntilPeriod)
            {
                return $query->has('pinjaman')->whereBetween('tgl_transaksi',[$startUntilPeriod,$endUntilPeriod]);
            });
        }
        elseif(isset($request->type_id) && $request->type_id == JKK_PRINTED_TYPE_PENARIKAN_SIMPANAN)
        {
            $jkkPrinted = $jkkPrinted->whereHas('jkkPenarikan', function ($query) use ($startUntilPeriod,&$endUntilPeriod)
            {
                return $query->whereBetween('tgl_transaksi',[$startUntilPeriod,$endUntilPeriod]);
            }

        );
        }
        else
        {
            $jkkPrinted = $jkkPrinted->whereHas('jkkPenarikan', function ($query1) use ($startUntilPeriod,&$endUntilPeriod)
            {
                return $query1->whereBetween('tgl_transaksi',[$startUntilPeriod,$endUntilPeriod]);
            })
                                    ->orWhereHas('jkkPengajuan', function ($query)use ($startUntilPeriod,&$endUntilPeriod)
                                    {
                                        return $query->has('pinjaman')->whereBetween('tgl_transaksi',[$startUntilPeriod,$endUntilPeriod]);
                                    });
        }

        return DataTables::eloquent($jkkPrinted)
                            ->make(true);
    }

    public function reprint(Request $request, $id)
    {
        $user = Auth::user();
        $this->authorize('print jkk', $user);

        $jkkPrinted = JkkPrinted::findOrFail($id);
        $config['disk'] = 'upload';
        $config['upload_path'] = '/reprintJKK/'.$id.'/paymentConfirmation';
        $config['public_path'] = env('APP_URL') . '/reprintJKK/'.$id.'/paymentConfirmation';
        if (!Storage::disk($config['disk'])->has($config['upload_path']))
        {
            Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
        }
        if ($request->payment_confirmation){
            if ($request->payment_confirmation->isValid())
        {
            $filename = uniqid() .'.'. $request->payment_confirmation->getClientOriginalExtension();

            Storage::disk($config['disk'])->putFileAs($config['upload_path'], $request->payment_confirmation, $filename);
            $jkkPrinted->payment_confirmation_path = $config['disk'].$config['upload_path'].'/'.$filename;
        }
        }

        $jkkPrinted->save();

        if ($jkkPrinted->isPenarikanSimpanan())
        {
            return $this->reprintPenarikan($jkkPrinted);
        }
        else
        {
            return $this->reprintPengajuanPinjaman($jkkPrinted);
        }
    }

    public function reprintPenarikan(JkkPrinted $jkkPrinted)
    {
        $listPenarikan = $jkkPrinted->jkkPenarikan;

        $data['tgl_print']= $jkkPrinted->printed_at;
        $data['listPenarikan'] = $listPenarikan;
        $data['no_jkk'] = $jkkPrinted->jkk_number;
        $data['jenisSimpanan'] = JenisSimpanan::all();
        $data['reprint'] = 'reprint';
        view()->share('data', $data);
        PDF::setOptions(['margin-left' => 0, 'margin-right' => 0]);
        $pdf = PDF::loadView('penarikan.pdfJKK', $data)->setPaper('a4', 'landscape');

        // download PDF file with download method
        $filename = $jkkPrinted->jkk_number . '.pdf';
        return $pdf->download($filename);
    }

    public function reprintPengajuanPinjaman(JkkPrinted $jkkPrinted)
    {
        $listPengajuan = $jkkPrinted->jkkPengajuan;
        if ($listPengajuan->count())
        {
            foreach ($listPengajuan as $key => $pengajuan)
            {
                if (is_null($pengajuan->pinjaman))
                {
                    $listPengajuan->forget($key);
                }
            }
        }

        if (!$listPengajuan->count())
        {
            return redirect()->back()->withErrors('Pinjaman untuk jkk ini belum di generate. silahkan periksa kembali');
        }

        $data['listPengajuan'] = $listPengajuan;
        $data['tgl_print'] = $jkkPrinted->printed_at;
        $data['no_jkk'] = $jkkPrinted->jkk_number;
        $data['reprint'] = 'reprint';
        view()->share('data',$data);
        PDF::setOptions(['margin-left' => 0,'margin-right' => 0]);
        $pdf = PDF::loadView('pinjaman.printJKK', $data)->setPaper('a4', 'landscape');

        // download PDF file with download method
        $filename = $jkkPrinted->jkk_number.'.pdf';
        return $pdf->download($filename);

        // return view('pinjaman.printJKK', $data);
    }

    public function show($id)
    {
        try
        {
            $jkkPrinted = JkkPrinted::findOrFail($id);
            $bankAccounts = Code::where('CODE', 'like', '102%')->where('is_parent', 0)->get();
            $data['title'] = $jkkPrinted->jkk_number;
            $data['jkk'] = $jkkPrinted;
            $data['listPengajuan'] = $jkkPrinted->jkkPengajuan;
            $data['listPenarikan'] = $jkkPrinted->jkkPenarikan;
            $data['bankAccounts'] = $bankAccounts;
            return view('jkk.detail', $data);
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }
}
