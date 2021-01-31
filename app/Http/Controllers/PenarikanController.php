<?php

namespace App\Http\Controllers;

use App\Events\Penarikan\PenarikanApproved;
use Illuminate\Http\Request;

use App\Events\Penarikan\PenarikanCreated;
use App\Events\Penarikan\PenarikanUpdated;
use App\Exports\PenarikanExport;
use App\Imports\PenarikanImport;
use App\Managers\PenarikanManager;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\Simpanan;
use App\Models\Tabungan;

use Carbon\Carbon;
use DB;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            $data['jenisSimpanan'] = JenisSimpanan::all();
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
            $tabungan = $anggota->tabungan->where('kode_trans', $request->jenis_simpanan)->first();
            $besarPenarikan = filter_var($request->besar_penarikan, FILTER_SANITIZE_NUMBER_INT);
            
            if (is_null($tabungan))
            {
                return redirect()->back()->withError($anggota->nama_anggota. " belum memiliki tabungan");
            }
            else if($tabungan->totalBesarTabungan < $besarPenarikan)
            {
                return redirect()->back()->withError("Saldo tabungan tidak mencukupi");
            }

            $penarikan = new Penarikan();
            DB::transaction(function () use ($besarPenarikan, $anggota, $tabungan, &$penarikan, $user)
            {
                $penarikan->kode_anggota = $anggota->kode_anggota;
                $penarikan->kode_tabungan = $tabungan->kode_tabungan;
                $penarikan->id_tabungan = $tabungan->id;
                $penarikan->besar_ambil = $besarPenarikan;
                $penarikan->code_trans = $tabungan->kode_trans;
                $penarikan->tgl_ambil = Carbon::now();
                $penarikan->u_entry = $user->name;
                $penarikan->created_by = $user->id;
                $penarikan->status_pengambilan = STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI;
                $penarikan->save();
            });

            event(new PenarikanCreated($penarikan, $tabungan));
            // return redirect()->route('penarikan-receipt', ['id' => $penarikan->kode_ambil])->withSuccess("Penarikan berhasil");
            return redirect()->back()->withSuccess('Permintaan penarikan berhasil disimpan dan dalam proses persetujuan');
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
        $saldoTabungan = Tabungan::where('kode_anggota', $id)->get();

        $thisYear = Carbon::now()->year;
        $listSimpanan = Simpanan::where('kode_anggota', $id)
                                ->whereYear('tgl_entri', $thisYear)
                                ->orderBy('tgl_entri','asc')
                                ->get();

        $groupedListSimpanan = $listSimpanan->groupBy('kode_jenis_simpan');
        $saldoTabungan = $saldoTabungan->map(function ($tabungan) use ($groupedListSimpanan)
        {
            $res = $tabungan;
            if (isset($groupedListSimpanan[$tabungan->kode_trans]))
            {
                $totalSimpanan = $groupedListSimpanan[$tabungan->kode_trans]->sum('besar_simpanan');
                $res->besar_tabungan = $tabungan->besar_tabungan + $totalSimpanan;
            }
            return $res;
        });
        
        $data['anggota'] = $anggota;
        $data['saldoTabungan'] = $saldoTabungan;
        return view('penarikan.detailAnggota', $data);
    }

    public function receipt($id)
    {
        $user = Auth::user();
        $this->authorize('add penarikan', $user);

        $data['title'] = 'Bukti Pengambilan Tunai';
        $penarikan = Penarikan::findOrFail($id);
        $tabungan = Tabungan::where('kode_anggota', $penarikan->kode_anggota)
                            ->where('kode_trans', $penarikan->code_trans)
                            ->first();
        $data['penarikan'] = $penarikan;
        $data['tabungan'] = $tabungan;
        return view('penarikan.receipt', $data);
    }

    public function downloadReceipt($id)
    {
        $user = Auth::user();
        $this->authorize('add penarikan', $user);

        $penarikan = Penarikan::findOrFail($id);
        $tabungan = Tabungan::where('kode_anggota', $penarikan->kode_anggota)
                            ->where('kode_trans', $penarikan->code_trans)
                            ->first();
        $penarikan->tabungan = $tabungan;
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
        if ($user->isAnggota())
        {
            $listPenarikan = $listPenarikan->where('kode_anggota', $user->anggota->kode_anggota);
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

    public function index(Request $request)
    {
        try
        {
            $user = Auth::user();
            if ($user->isAnggota()) {
                $anggota = $user->anggota;
                if (is_null($anggota)) {
                    return redirect()->back()->withError('Your account has no members');
                }
    
                $listPenarikan = Penarikan::where('kode_anggota', $anggota->kode_anggota)
                        ->get();
            } else {
                $listPenarikan = Penarikan::with('anggota')->get();
            }

            $data['title'] = "List Penarikan Pinjaman";
            $data['listPenarikan'] = $listPenarikan;
            $data['request'] = $request;
            return view('penarikan.index', $data);
        }
        catch (\Throwable $e)
        {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function updateStatus(Request $request)
    {
        try
        {
            $user = Auth::user();
            $check = Hash::check($request->password, $user->password);
            if (!$check) {
                Log::error('Wrong Password');
                return response()->json(['message' => 'Wrong Password'], 412);
            }

            $penarikan = Penarikan::find($request->id);
            if ($request->status == STATUS_PENGAMBILAN_DIBATALKAN) {
                $penarikan->status_penarikan = STATUS_PENGAMBILAN_DIBATALKAN;
                $penarikan->save();
                return response()->json(['message' => 'success'], 200);
            }

            $this->authorize('approve penarikan', $user);
            if (is_null($penarikan)) {
                return response()->json(['message' => 'not found'], 404);
            }

            if ($request->status == STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_BENDAHARA) {
                $statusPenarikanSekarang = $penarikan->statusPenarikan;
                if ($penarikan->besar_pinjam <= $statusPenarikanSekarang->batas_pengajuan) {
                    $penarikan->status_pengambilan = STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN;
                } else {
                    $penarikan->status_pengambilan = $request->status;
                }
            } elseif ($request->status == STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_KETUA) {
                $statusPenarikanSekarang = $penarikan->statusPenarikan;
                if ($penarikan->besar_pinjam <= $statusPenarikanSekarang->batas_pengajuan) {
                    $penarikan->status_pengambilan = STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN;
                } else {
                    $penarikan->status_pengambilan = $request->status;
                }
            } else {
                $penarikan->status_pengambilan = $request->status;
            }

            $penarikan->tgl_acc = Carbon::now();
            $penarikan->approved_by = $user->id;

            if ($request->status == STATUS_PENGAMBILAN_DITERIMA) {
                $penarikan->paid_by_cashier = $user->id;
                $file = $request->bukti_pembayaran;

                if ($file) {
                    $config['disk'] = 'upload';
                    $config['upload_path'] = '/pinjaman/pengajuan/' . $penarikan->kode_ambil . '/bukti-pembayaran/';
                    $config['public_path'] = env('APP_URL') . '/upload/pinjaman/pengajuan/' . $penarikan->kode_ambil . '/bukti-pembayaran/';

                    // create directory if doesn't exist
                    if (!Storage::disk($config['disk'])->has($config['upload_path'])) {
                        Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
                    }

                    // upload file if valid
                    if ($file->isValid()) {
                        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                        Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file, $filename);
                        $penarikan->bukti_pembayaran = $config['disk'] . $config['upload_path'] . '/' . $filename;
                    }
                }
            }

            $penarikan->save();
            if ($penarikan->menungguPembayaran()) {
                event(new PenarikanApproved($penarikan));
            }
            event(new PenarikanUpdated($penarikan));

            return response()->json(['message' => 'success'], 200);
        }
        catch (\Exception $e)
        {
            Log::error($e);
            $message = $e->getMessage();
            return response()->json(['message' => $message], 500);
        }
    }

    public function printJkk()
    {
        try
        {
            $listPenarikan = Penarikan::needPrintJkk()
                                            ->menungguPembayaran()
                                            ->get();

            $data['title'] = "Print JKK";
            $data['listPenarikan'] = $listPenarikan;
            return view('penarikan.printJKK',$data);
        }
        catch (\Throwable $e)
        {
            Log::error($e);
            $message = $e->getMessage();
            return response()->json(['message' => $message], 500);
        }
    }

    public function storePrintJkk(Request $request)
    {
        try
        {
            $listPenarikan = Penarikan::whereIn('kode_ambil', $request->kode_ambil)
                                        ->get();

            foreach ($listPenarikan as $penarikan)
            {
                $penarikan->no_jkk = $request->no_jkk;
                $penarikan->status_jkk = 1;
                $penarikan->save();
            }
            
            $data['listPenarikan'] = $listPenarikan;
            view()->share('data',$data);
            PDF::setOptions(['margin-left' => 0,'margin-right' => 0]);
            $pdf = PDF::loadView('penarikan.pdfJKK', $data)->setPaper('a4', 'landscape');

            // download PDF file with download method
            $filename = $request->no_jkk.'-'.Carbon::now()->toDateString().'.pdf';
            return $pdf->download($filename);

            // return view('penarikan.pdfJKK', $data);
        }
        catch (\Throwable $e)
        {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function detailTransfer($id)
    {
        $penarikan = Penarikan::find($id);
        if (is_null($penarikan)) {
            return response()->json(['message' => 'Penarikan Not Found'], 404);
        }

        $data['penarikan'] = $penarikan;
        return view('penarikan.detailTransfer', $data);
    }
}
