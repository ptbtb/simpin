<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Events\Pinjaman\PengajuanApproved;
use App\Exports\PinjamanExport;

use App\Managers\PinjamanManager;

use App\Models\Pengajuan;
use App\Models\Pinjaman;
use App\Models\JenisPinjaman;

use Auth;
use Carbon\Carbon;
use DB;
use Excel;
use PDF;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        $anggota = $user->anggota;
        if (is_null($anggota))
        {
            return redirect()->back()->withError('Your account has no members');
        }
        
        $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                                ->where('status', 'belum lunas');;
        if ($request->from)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','<=', $request->to);
        }
        $listPinjaman = $listPinjaman->get();
        $data['title'] = "List Pinjaman";
        $data['listPinjaman'] = $listPinjaman;
        $data['request'] = $request;
        return view('pinjaman.index',$data);
    }

    public function indexPengajuan(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pengajuan pinjaman', $user);

        if ($user->isAnggota())
        {
            $anggota = $user->anggota;
            if (is_null($anggota))
            {
                return redirect()->back()->withError('Your account has no members');
            }
            
            $listPengajuanPinjaman = Pengajuan::where('kode_anggota', $anggota->kode_anggota)
                                                ->get();
        }
        else
        {
            $listPengajuanPinjaman = Pengajuan::with('anggota')->get();
        }
        
        $data['title'] = "List Pengajuan Pinjaman";
        $data['listPengajuanPinjaman'] = $listPengajuanPinjaman;
        $data['request'] = $request;
        return view('pinjaman.indexPengajuan',$data);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history pinjaman', $user);

        $anggota = $user->anggota;
        if (is_null($anggota))
        {
            return redirect()->back()->withError('Your account has no members');
        }
        
        $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                                ->where('status', 'lunas');
        if ($request->from)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','<=', $request->to);
        }
        $listPinjaman = $listPinjaman->get();
        $data['title'] = "History Pinjaman";
        $data['listPinjaman'] = $listPinjaman;
        $data['request'] = $request;
        return view('pinjaman.history',$data);
    }

    public function show($id)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        $anggota = $user->anggota;
        if (is_null($anggota))
        {
            return redirect()->back()->withError('Your account has no members');
        }

        $pinjaman = Pinjaman::with('anggota','listAngsuran')
                            ->where('kode_anggota', $anggota->kode_anggota)
                            ->where('kode_pinjam', $id)
                            ->first();
        
        $data['pinjaman'] = $pinjaman;
        $data['jenisPinjaman'] = $pinjaman->jenisPinjaman;
        return view('pinjaman.detail', $data);
    }

    public function createExcel(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);
        $anggota = $user->anggota;
        $request->anggota = $anggota;
        $filename = 'export_pinjaman_excel_'.Carbon::now()->format('d M Y').'.xlsx';
        return Excel::download(new PinjamanExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function createPDF(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        $anggota = $user->anggota;
        $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota);
        if ($request->from)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','<=', $request->to);
        }
        if ($request->status)
        {
            $listPinjaman = $listPinjaman->where('status', $request->status);
        }

        $listPinjaman = $listPinjaman->get();

        // share data to view
        view()->share('listPinjaman',$listPinjaman);
        $pdf = PDF::loadView('pinjaman.excel', $listPinjaman)->setPaper('a4', 'landscape');
  
        // download PDF file with download method
        $filename = 'export_pinjaman_'.Carbon::now()->format('d M Y').'.pdf';
        return $pdf->download($filename);
    }

    public function createPDF1(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);
        $anggota = $user->anggota;
        $request->anggota = $anggota;
        $filename = 'export_pinjaman_excel_'.Carbon::now()->format('d M Y').'.pdf';
        return Excel::download(new PinjamanExport($request), $filename, \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function createPengajuanPinjaman()
    {
        $user = Auth::user();
        $this->authorize('add pengajuan pinjaman', $user);
        $data['title'] = 'Buat Pengajuan Pinjaman';
        $data['listJenisPinjaman'] = JenisPinjaman::all();
        return view('pinjaman.createPengajuanPinjaman', $data);
    }

    public function storePengajuanPinjaman(Request $request)
    {
        $user = Auth::user();
        $this->authorize('add pengajuan pinjaman', $user);
        DB::transaction(function () use ($request)
        {
            $pengajuan = new Pengajuan();
            $pengajuan->tgl_pengajuan = Carbon::now();
            $pengajuan->kode_anggota = $request->kode_anggota;
            $pengajuan->kode_jenis_pinjam = $request->jenis_pinjaman;
            $pengajuan->besar_pinjam = $request->besar_pinjaman;
            $pengajuan->status = 'submited';
            $pengajuan->save(); 
        });
        
        return redirect()->route('pengajuan-pinjaman-add')->withSuccess('Pengajuan pinjaman telah dibuat dan menunggu persetujuan.');
    }

    public function updateStatusPengajuanPinjaman(Request $request)
    {
        try
        {
            $pengajuan = Pengajuan::find($request->id);
            if (is_null($pengajuan))
            {
                return response()->json(['message' => 'not found'], 404);
            }
            if ($request->action == APPROVE_PENGAJUAN_PINJAMAN)
            {
                $pengajuan->status = "diterima";
                $pengajuan->tgl_acc = Carbon::now();
                $pengajuan->save();
                event(new PengajuanApproved($pengajuan));
            }
            else
            {
                $pengajuan->status = "ditolak";
                $pengajuan->save();
            }
            return response()->json(['message' => 'success'], 200);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            return response()->json(['message' => $message], 500);
        }
    }
}