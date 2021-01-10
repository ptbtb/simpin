<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            $listPengajuan = Pengajuan::whereIn('kode_pengajuan', $request->kode_pengajuan)
                                        ->get();

            foreach ($listPengajuan as $pengajuan)
            {
                $pengajuan->no_jkk = $request->no_jkk;
                $pengajuan->status_jkk = 1;
                $pengajuan->save();
            }
            
            $data['listPengajuan'] = $listPengajuan;
            view()->share('data',$data);
            PDF::setOptions(['margin-left' => 0,'margin-right' => 0]);
            $pdf = PDF::loadView('pinjaman.printJKK', $data)->setPaper('a4', 'landscape');

            // download PDF file with download method
            $filename = $request->no_jkk.'.pdf';
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
