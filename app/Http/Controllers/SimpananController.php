<?php

namespace App\Http\Controllers;

use App\Exports\SimpananExport;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Auth;
use Carbon\Carbon;
use Excel;
use PDF;

class SimpananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view simpanan', Auth::user());
        $simpanans = DB::table('simpanan')
                ->get();
        $data['simpanans'] = $simpanans;
        return view('/simpanan/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $this->authorize('view simpanan', $user);

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
        $listSimpanan = $listSimpanan->get();

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
        $this->authorize('view simpanan', $user);
        $anggota = $user->anggota;
        $request->anggota = $anggota;
        $filename = 'export_simpanan_excel_'.Carbon::now()->format('d M Y').'.xlsx';
        return Excel::download(new SimpananExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
}
