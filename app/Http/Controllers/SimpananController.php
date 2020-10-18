<?php

namespace App\Http\Controllers;

use App\Exports\SimpananExport;
use App\Models\JenisSimpanan;
use App\Models\KodeTransaksi;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Auth;
use Carbon\Carbon;
use Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class SimpananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view simpanan', Auth::user());
        $listJenisSimpanan = JenisSimpanan::all(); 
        $data['title'] = "List Transaksi Simpanan";
        $data['request'] = $request;
        $data['listJenisSimpanan'] = $listJenisSimpanan;
        return view('simpanan.index', $data);
    }

    public function indexAjax(Request $request)
    {
        $this->authorize('view simpanan', Auth::user());
        $simpanan = Simpanan::with('anggota');
        \Log::info($request);
        if ($request->from)
        {
            $simpanan = $simpanan->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $simpanan = $simpanan->where('tgl_entri','<=', $request->to);
        }
        if ($request->jenisSimpanan)
        {
            $simpanan = $simpanan->where('jenis_simpan',$request->jenisSimpanan);
        }
        $simpanan = $simpanan->orderBy('tgl_entri','asc')->take(100);
        return DataTables::eloquent($simpanan)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add simpanan', Auth::user());
        $listJenisSimpanan = JenisSimpanan::all(); 
        $data['title'] = "List Transaksi Simpanan";
        $data['listJenisSimpanan'] = $listJenisSimpanan;
        $data['listKodeTransaksi'] = KodeTransaksi::all();
        // dd($data);
        return view('simpanan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('add simpanan', Auth::user());
        try
        {
            $besarSimpanan = $request->besar_simpanan;
            $jenisSimpanan = JenisSimpanan::find($request->jenis_simpanan);

            $simpanan = new Simpanan();
            $simpanan->jenis_simpan = strtoupper($jenisSimpanan->nama_simpanan);
            $simpanan->besar_simpanan = $besarSimpanan;
            $simpanan->kode_anggota = $request->kode_anggota;
            $simpanan->u_entry = Auth::user()->name;
            $simpanan->tgl_entri = Carbon::now();
            $simpanan->code_trans = $request->kode_transaksi;
            $simpanan->keterangan = ($request->keterangan)? $request->keterangan:null;
            $simpanan->save();

            return redirect()->back()->withSuccess('Berhasil menambah transaksi');
        }
        catch (\Throwable $th)
        {
            return redirect()->back()->withError('Gagal menyimpan data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history simpanan', $user);

        if ($user->roles->first()->id == ROLE_ANGGOTA)
        {
            $anggota = $user->anggota;
            if (is_null($anggota))
            {
                return redirect()->back()->withError('Your account has no members');
            }
            
            $listSimpanan = Simpanan::where('kode_anggota', $anggota->kode_anggota);
        }
        else
        {
            $listSimpanan = Simpanan::with('anggota');
        }

        if ($request->from)
        {
            $listSimpanan = $listSimpanan->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listSimpanan = $listSimpanan->where('tgl_entri','<=', $request->to);
        }
        $listSimpanan = $listSimpanan->orderBy('tgl_entri','desc')->take(200)->get();
        $data['title'] = "History Simpanan";
        $data['listSimpanan'] = $listSimpanan;
        $data['request'] = $request;
        return view('simpanan.history',$data);
    }

    public function createPDF(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history simpanan', $user);

        if ($user->roles->first()->id == ROLE_ANGGOTA)
        {
            $anggota = $user->anggota;
            if (is_null($anggota))
            {
                return redirect()->back()->withError('Your account has no members');
            }
            
            $listSimpanan = Simpanan::where('kode_anggota', $anggota->kode_anggota);
        }
        else
        {
            $listSimpanan = Simpanan::with('anggota');
        }

        if ($request->from)
        {
            $listSimpanan = $listSimpanan->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listSimpanan = $listSimpanan->where('tgl_entri','<=', $request->to);
        }
        if ($request->jenis_simpanan)
        {
            $listSimpanan = $listSimpanan->where('jenis_simpan',$request->jenis_simpanan);
        }
        // $listSimpanan = $listSimpanan->get();
        $listSimpanan = $listSimpanan->orderBy('tgl_entri','desc')->take(5)->get();

        // share data to view
        view()->share('listSimpanan',$listSimpanan);
        $pdf = PDF::loadView('simpanan.excel', $listSimpanan)->setPaper('a4', 'landscape');
  
        // download PDF file with download method
        $filename = 'export_simpanan_'.Carbon::now()->format('d M Y').'.pdf';
        return $pdf->download($filename);
    }

    public function createExcel(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history simpanan', $user);
        if ($user->roles->first()->id == ROLE_ANGGOTA)
        {
            $anggota = $user->anggota;
            $request->anggota = $anggota;
        }
        
        $filename = 'export_simpanan_excel_'.Carbon::now()->format('d M Y').'.xlsx';
        return Excel::download(new SimpananExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
}
