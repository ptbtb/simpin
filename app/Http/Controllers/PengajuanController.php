<?php

namespace App\Http\Controllers;

use App\Models\JkkPrinted;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use DB;
use PDF;

class PengajuanController extends Controller
{
    public function indexJkk()
    {
        try
        {
            $listPengajuanPinjaman = Pengajuan::needPrintJkk()
                                            ->menungguPembayaran()
                                            ->get();

            $data['title'] = "Print JKK";
            $data['listPengajuanPinjaman'] = $listPengajuanPinjaman;
            return view('pinjaman.indexPrintJKK',$data);
        }
        catch (\Throwable $e)
        {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }
    public function printJkk(Request $request)
    {
        try
        {
            //start create sequence number
            $bookno_jkk= DB::select("SELECT NEXTVAL(jkk_sequence) as nextnum")[0]->nextnum;
            $no_jkk = Carbon::createFromFormat('Y-m-d', $request->tgl_print)->format('y').Carbon::createFromFormat('Y-m-d', $request->tgl_print)->format('m').Carbon::createFromFormat('Y-m-d', $request->tgl_print)->format('d').sprintf('%04d', $bookno_jkk);
           
            //end create sequence number


            $listPengajuan = Pengajuan::whereIn('kode_pengajuan', $request->kode_pengajuan)
                                        ->get();

            foreach ($listPengajuan as $pengajuan)
            {
                $pengajuan->no_jkk = $no_jkk;
                $pengajuan->status_jkk = 1;
                $pengajuan->save();
            }

            // create jkkprinted
            $jkkPrinted = JkkPrinted::where('jkk_number',  $request->no_jkk)->first();
            if (is_null($jkkPrinted))
            {
                $jkkPrinted = new JkkPrinted();
                $jkkPrinted->jkk_number = $no_jkk;
                $jkkPrinted->jkk_printed_type_id = JKK_PRINTED_TYPE_PENGAJUAN_PINJAMAN;
                $jkkPrinted->printed_at = Carbon::createFromFormat('Y-m-d', $request->tgl_print);
                $jkkPrinted->printed_by = Auth::user()->id;
                $jkkPrinted->save();
            }

            $data['listPengajuan'] = $listPengajuan;
            $data['no_jkk'] = $no_jkk;
            $data['tgl_print']=Carbon::createFromFormat('Y-m-d', $request->tgl_print);
            view()->share('data',$data);
            PDF::setOptions(['margin-left' => 0,'margin-right' => 0]);
            $pdf = PDF::loadView('pinjaman.printJKK', $data)->setPaper('a4', 'landscape');

            // download PDF file with download method
            $filename = $no_jkk.'.pdf';
            return $pdf->download($filename);

            // return view('pinjaman.printJKK', $data);
        }
        catch (\Throwable $e)
        {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }
}
