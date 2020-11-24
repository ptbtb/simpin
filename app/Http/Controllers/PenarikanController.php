<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Events\Penarikan\PenarikanCreated;
use App\Exports\PenarikanExport;
use App\Imports\PenarikanImport;
use App\Managers\PenarikanManager;
use App\Models\Anggota;
use App\Models\Penarikan;
use App\Models\Tabungan;

use Auth;
use Carbon\Carbon;
use DB;
use Excel;
use Hash;
use PDF;

class PenarikanController extends Controller
{
    public function create()
    {
        try
        {
            $user = Auth::user();
            $this->authorize('add penarikan', $user);

            $data['title'] = "Buat Penarikan";
            return view('penarikan.create', $data);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
				$message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $user = Auth::user();
            $this->authorize('add penarikan', $user);

            // check password
            $check = Hash::check($request->password, $user->password);
            if (!$check)
            {
                return redirect()->back()->withError("Password yang anda masukkan salah");
            }

            $anggota = Anggota::with('tabungan')->find($request->kode_anggota);
            $tabungan = $anggota->tabungan;
            $besarPenarikan = filter_var($request->besar_penarikan, FILTER_SANITIZE_NUMBER_INT);

            if (is_null($tabungan))
            {
                return redirect()->back()->withError($anggota->nama_anggota. " belum memiliki tabungan");
            }
            else if($tabungan->besar_tabungan < $besarPenarikan)
            {
                return redirect()->back()->withError("Saldo tabungan tidak mencukupi");
            }

            $penarikan = new Penarikan();
            DB::transaction(function () use ($besarPenarikan, $anggota, $tabungan, &$penarikan)
            {
                $penarikan->kode_anggota = $anggota->kode_anggota;
                $penarikan->kode_tabungan = $tabungan->kode_tabungan;
                $penarikan->besar_ambil = $besarPenarikan;
                $penarikan->tgl_ambil = Carbon::now();
                $penarikan->save();
            });

            event(new PenarikanCreated($penarikan));
            return redirect()->route('penarikan-receipt', ['id' => $penarikan->kode_ambil])->withSuccess("Penarikan berhasil");
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
				$message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
        }
    }

    public function detailAnggota($id)
    {
        $user = Auth::user();
        $this->authorize('add penarikan', $user);

        $anggota = Anggota::with('tabungan')->find($id);
        $data['anggota'] = $anggota;
        return view('penarikan.detailAnggota', $data);
    }

    public function receipt($id)
    {
        $user = Auth::user();
        $this->authorize('add penarikan', $user);

        $data['title'] = 'Bukti Pengambilan Tunai';
        $penarikan = Penarikan::findOrFail($id);
        $data['penarikan'] = $penarikan;
        return view('penarikan.receipt', $data);
    }

    public function downloadReceipt($id)
    {
        $user = Auth::user();
        $this->authorize('add penarikan', $user);

        $penarikan = Penarikan::findOrFail($id);
        // share data to view
        view()->share('penarikan',$penarikan);
        $pdf = PDF::loadView('penarikan.receiptpdf', $penarikan)->setPaper('a4', 'portrait');
  
        // download PDF file with download method
        $filename = 'receipt_penarikan_'.$penarikan->anggota->nama_anggota."_".Carbon::now()->format('d M Y').'.pdf';
        return $pdf->download($filename);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history penarikan', $user);

        $listPenarikan = Penarikan::with('anggota');

        if ($request->kode_anggota)
        {
            $listPenarikan = $listPenarikan->where('kode_anggota', $request->kode_anggota);
        }

        if ($request->from)
        {
            $listPenarikan = $listPenarikan->where('tgl_ambil','>=', $request->from);
        }
        if ($request->to)
        {
            $listPenarikan = $listPenarikan->where('tgl_ambil','<=', $request->to);
        }

        $listPenarikan = $listPenarikan->orderBy('tgl_ambil','desc')
                                        ->has('anggota')
                                        ->get();
                                        
		$data['title'] = 'History Penarikan';
        $data['request'] = $request;
        $data['listPenarikan'] = $listPenarikan;
		return view('penarikan.history', $data);
    }

    public function createPDF(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history penarikan', $user);

        $listPenarikan = Penarikan::with('anggota');

        if ($request->kode_anggota)
        {
            $listPenarikan = $listPenarikan->where('kode_anggota', $request->kode_anggota);
        }

        if ($request->from)
        {
            $listPenarikan = $listPenarikan->where('tgl_ambil','>=', $request->from);
        }
        if ($request->to)
        {
            $listPenarikan = $listPenarikan->where('tgl_ambil','<=', $request->to);
        }

        $listPenarikan = $listPenarikan->orderBy('tgl_ambil','desc')
                                        ->has('anggota')
                                        ->get();

        // share data to view
        view()->share('listPenarikan',$listPenarikan);
        $pdf = PDF::loadView('penarikan.excel', $listPenarikan)->setPaper('a4', 'landscape');
  
        // download PDF file with download method
        $filename = 'export_history_penarikan_'.Carbon::now()->format('d M Y').'.pdf';
        return $pdf->download($filename);
    }

    public function createExcel(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history penarikan', $user);
        $filename = 'export_transaksi_excel_'.Carbon::now()->format('d M Y').'.xlsx';
        return Excel::download(new PenarikanExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function importExcel()
    {
        $data['title'] = 'Import Transaksi Penarikan';
		return view('penarikan.import', $data);
    }

    public function storeImportExcel(Request $request)
    {
        $this->authorize('import penarikan', Auth::user());
        try
        {
            Excel::import(new PenarikanImport, $request->file);
            return redirect()->back()->withSuccess('Import data berhasil');
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
            return redirect()->back()->withError('Gagal import data');
        }
    }
}
