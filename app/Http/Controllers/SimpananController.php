<?php

namespace App\Http\Controllers;

use App\Exports\KartuSimpananExport;
use App\Exports\SimpananExport;
use App\Imports\SimpananImport;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\Simpanan;
use Illuminate\Http\Request;

use Auth;
use Hash;
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
        $data['title'] = "List Transaksi Simpanan";
        $data['request'] = $request;
        return view('simpanan.index', $data);
    }

    public function indexAjax(Request $request)
    {
        $this->authorize('view simpanan', Auth::user());
        $simpanan = Simpanan::with('anggota');
        if ($request->from || $request->to)
        {
            if ($request->from)
            {
                $simpanan = $simpanan->where('tgl_entri','>=', $request->from);
            }
            if ($request->to)
            {
                $simpanan = $simpanan->where('tgl_entri','<=', $request->to);
            }
        }
        else
        {
            $from = Carbon::now()->addDays(-30)->format('Y-m-d');
            $to = Carbon::now()->format('Y-m-d');
            $simpanan = $simpanan->where('tgl_entri','>=', $from)
                                ->where('tgl_entri','<=', $to);
        }
        if ($request->jenis_simpanan)
        {
            $simpanan = $simpanan->where('kode_jenis_simpan',$request->jenis_simpanan);
        }
        $simpanan = $simpanan->orderBy('tgl_entri','desc');
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
            // check password
            $check = Hash::check($request->password, Auth::user()->password);
            if (!$check)
            {
                return redirect()->back()->withError("Password yang anda masukkan salah");
            }

            $besarSimpanan = filter_var($request->besar_simpanan, FILTER_SANITIZE_NUMBER_INT);
            $jenisSimpanan = JenisSimpanan::find($request->jenis_simpanan);

            $simpanan = new Simpanan();
            $simpanan->jenis_simpan = strtoupper($jenisSimpanan->nama_simpanan);
            $simpanan->besar_simpanan = $besarSimpanan;
            $simpanan->kode_anggota = $request->kode_anggota;
            $simpanan->u_entry = Auth::user()->name;
            $simpanan->tgl_entri = Carbon::now();
            $simpanan->kode_jenis_simpan = $jenisSimpanan->kode_jenis_simpan;
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
        $data['title'] = "History Simpanan";
        $data['request'] = $request;
        return view('simpanan.history',$data);
    }

    public function historyData(Request $request)
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

        if ($request->from || $request->to)
        {
            if ($request->from)
            {
                $listSimpanan = $listSimpanan->where('tgl_entri','>=', $request->from);
            }
            if ($request->to)
            {
                $listSimpanan = $listSimpanan->where('tgl_entri','<=', $request->to);
            }
        }
        else
        {
            $from = Carbon::now()->addDays(-30)->format('Y-m-d');
            $to = Carbon::now()->format('Y-m-d');
            $listSimpanan = $listSimpanan->where('tgl_entri','>=', $from)
                                        ->where('tgl_entri','<=', $to);
        }
        $listSimpanan = $listSimpanan->orderBy('tgl_entri','desc');
        return DataTables::eloquent($listSimpanan)->make(true);
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

        if ($request->from || $request->to)
        {
            if ($request->from)
            {
                $listSimpanan = $listSimpanan->where('tgl_entri','>=', $request->from);
            }
            if ($request->to)
            {
                $listSimpanan = $listSimpanan->where('tgl_entri','<=', $request->to);
            }
        }
        else
        {
            $from = Carbon::now()->addDays(-30)->format('Y-m-d');
            $to = Carbon::now()->format('Y-m-d');
            $listSimpanan = $listSimpanan->where('tgl_entri','>=', $from)
                                        ->where('tgl_entri','<=', $to);
        }
        if ($request->jenis_simpanan)
        {
            $listSimpanan = $listSimpanan->where('kode_jenis_simpan',$request->jenis_simpanan);
        }
        // $listSimpanan = $listSimpanan->get();
        $listSimpanan = $listSimpanan->orderBy('tgl_entri','desc')->get();

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

    public function importExcel()
    {
        $this->authorize('import simpanan', Auth::user());
        $data['title'] = 'Import Transaksi Simpanan';
        return view('simpanan.import', $data);
    }

    public function storeImportExcel(Request $request)
    {
        $this->authorize('import simpanan', Auth::user());
        try
        {
            Excel::import(new SimpananImport, $request->file);
            return redirect()->back()->withSuccess('Import data berhasil');
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
            return redirect()->back()->withError('Gagal import data');
        }
        
    }

    public function indexCard(Request $request)
    {
        try
        {
            $data['title'] = "Kartu Simpanan";
            return view('simpanan.card.index', $data);
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }

    public function showCard($kodeAnggota)
    {
        try
        {
            // get anggota
            $anggota = Anggota::with('tabungan')->findOrFail($kodeAnggota);

            // get this year
            $thisYear = Carbon::now()->year;

            // get list simpanan by this year and kode anggota. sort by tgl_entry ascending
            $listSimpanan = Simpanan::whereYear('tgl_entri', $thisYear)
                                    ->where('kode_anggota', $anggota->kode_anggota)
                                    ->orderBy('tgl_entri','asc')
                                    ->get();
                                    
            // data di grouping berdasarkan kode jenis simpan
            $groupedListSimpanan = $listSimpanan->groupBy('kode_jenis_simpan');

            // kode_jenis_simpan yang wajib ada
            $requiredKey = collect([JENIS_SIMPANAN_POKOK, JENIS_SIMPANAN_WAJIB, JENIS_SIMPANAN_SUKARELA]);

            // set default value untuk key yang tidak ada
            foreach ($requiredKey as $value)
            {
                if (!isset($groupedListSimpanan[$value]))
                {
                    $groupedListSimpanan[$value] = collect([]);
                }
            }

            $simpananKeys = $groupedListSimpanan->keys();
            $listPengambilan = Penarikan::where('kode_anggota', $anggota->kode_anggota)
                                        ->whereYear('tgl_ambil', $thisYear)
                                        ->whereIn('code_trans', $simpananKeys)
                                        ->orderBy('tgl_ambil', 'asc')
                                        ->get();
            /*
                tiap jenis simpanan di bagi jadi 3 komponen
                1. saldo akhir tahun tiap jenis simpanan
                2. list simpanan untuk tiap jenis simpanan pada tahun ini
                3. jumlah simpanan untuk tiap jenis simpanan pada tahun ini
                4. nama jenis simpanan
                5. total saldo akhir tiap jenis simpanan
            */

            $listSimpanan = [];
            $index = count($requiredKey);
            foreach ($groupedListSimpanan as $key => $list)
            {
                $jenisSimpanan = JenisSimpanan::find($key);
                if ($jenisSimpanan)
                {
                    $tabungan = $anggota->tabungan->where('kode_trans',$key)->first();
                    $res['name'] = $jenisSimpanan->nama_simpanan;
                    $res['balance'] = ($tabungan)? $tabungan->besar_tabungan:0;
                    $res['list'] = $list;
                    $res['amount'] = $list->sum('besar_simpanan');
                    $res['final_balance'] = $res['balance'] + $res['amount'];
                    $res['withdrawalList'] = $listPengambilan->where('code_trans', $key)->values();
                    $res['withdrawalAmount'] = $listPengambilan->where('code_trans', $key)->values()->sum('besar_ambil');
                    if ($key == JENIS_SIMPANAN_POKOK)
                    {
                        $listSimpanan[0] = (object)$res;
                    }
                    else if($key == JENIS_SIMPANAN_WAJIB)
                    {
                        $listSimpanan[1] = (object)$res;
                    }
                    else if($key == JENIS_SIMPANAN_SUKARELA)
                    {
                        $listSimpanan[2] = (object)$res;
                    }
                    else
                    {
                        $listSimpanan[$index] = (object)$res;
                        $index++;
                    }
                }
            }
                                    
            $data['anggota'] = $anggota;
            $data['listSimpanan'] = collect($listSimpanan)->sortKeys();
            
            return view('simpanan.card.detail', $data);
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }

    public function downloadPDFCard($kodeAnggota)
    {
        try
        {
            // get anggota
            $anggota = Anggota::findOrFail($kodeAnggota);

            // get this year
            $thisYear = Carbon::now()->year;

            // get list simpanan by this year and kode anggota. sort by tgl_entry ascending
            $listSimpanan = Simpanan::whereYear('tgl_entri', $thisYear)
                                    ->where('kode_anggota', $anggota->kode_anggota)
                                    ->orderBy('tgl_entri','asc')
                                    ->get();
                                    
            // data di grouping berdasarkan kode jenis simpan
            $groupedListSimpanan = $listSimpanan->groupBy('kode_jenis_simpan');

            // kode_jenis_simpan yang wajib ada
            $requiredKey = collect([JENIS_SIMPANAN_POKOK, JENIS_SIMPANAN_WAJIB, JENIS_SIMPANAN_SUKARELA]);

            // set default value untuk key yang tidak ada
            foreach ($requiredKey as $value)
            {
                if (!isset($groupedListSimpanan[$value]))
                {
                    $groupedListSimpanan[$value] = collect([]);
                }
            }

            $simpananKeys = $groupedListSimpanan->keys();
            $listPengambilan = Penarikan::where('kode_anggota', $anggota->kode_anggota)
                                        ->whereYear('tgl_ambil', $thisYear)
                                        ->whereIn('code_trans', $simpananKeys)
                                        ->orderBy('tgl_ambil', 'asc')
                                        ->get();
            /*
                tiap jenis simpanan di bagi jadi 3 komponen
                1. saldo akhir tahun tiap jenis simpanan
                2. list simpanan untuk tiap jenis simpanan pada tahun ini
                3. jumlah simpanan untuk tiap jenis simpanan pada tahun ini
                4. nama jenis simpanan
                5. total saldo akhir tiap jenis simpanan
            */

            $listSimpanan = [];
            $index = count($requiredKey);
            foreach ($groupedListSimpanan as $key => $list)
            {
                $jenisSimpanan = JenisSimpanan::find($key);
                if ($jenisSimpanan)
                {
                    $res['name'] = $jenisSimpanan->nama_simpanan;
                    $res['balance'] = 5000000;
                    $res['list'] = $list;
                    $res['amount'] = $list->sum('besar_simpanan');
                    $res['final_balance'] = $res['balance'] + $res['amount'];
                    $res['withdrawalList'] = $listPengambilan->where('code_trans', $key)->values();
                    $res['withdrawalAmount'] = $listPengambilan->where('code_trans', $key)->values()->sum('besar_ambil');
                    if ($key == JENIS_SIMPANAN_POKOK)
                    {
                        $listSimpanan[0] = (object)$res;
                    }
                    else if($key == JENIS_SIMPANAN_WAJIB)
                    {
                        $listSimpanan[1] = (object)$res;
                    }
                    else if($key == JENIS_SIMPANAN_SUKARELA)
                    {
                        $listSimpanan[2] = (object)$res;
                    }
                    else
                    {
                        $listSimpanan[$index] = (object)$res;
                        $index++;
                    }
                }
            }
                                    
            $data['anggota'] = $anggota;
            $data['listSimpanan'] = collect($listSimpanan)->sortKeys();
            
            // share data to view
            view()->share('data',$data);
            PDF::setOptions(['margin-left' => 0,'margin-right' => 0]);
            $pdf = PDF::loadView('simpanan.card.export2', $data)->setPaper('a4', 'portrait');
    
            // download PDF file with download method
            $filename = 'export_kartu_simpanan_'.Carbon::now()->format('d M Y').'.pdf';
            return $pdf->download($filename);
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }

    public function downloadExcelCard($kodeAnggota)
    {
        try
        {
            $filename = 'export_kartu_simpanan_excel_'.Carbon::now()->format('d M Y').'.xlsx';
            return Excel::download(new KartuSimpananExport($kodeAnggota), $filename);
        }
        catch (\Throwable $th)
        {
            \Log::error($e);
            return redirect()->back()->withError('Terjadi kesalahan sistem');
        }
    }
}
