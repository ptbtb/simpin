<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Code;
use App\Models\JurnalUmum;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use App\Managers\JurnalManager;

use Auth;
use DB;
use Hash;
use Carbon\Carbon;
use Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class JurnalUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view jurnal umum', Auth::user());
        $listJurnalUmum = JurnalUmum::with('code');
        $listJurnalUmum = $listJurnalUmum->orderBy('created_at','desc');

        $data['title'] = "List Jurnal Umum";
        $data['request'] = $request;
        $data['listJurnalUmum'] = $listJurnalUmum;

        return view('jurnal_umum.index', $data);
    }

    public function indexAjax(Request $request)
    {
        $this->authorize('view jurnal umum', Auth::user());
        $listJurnalUmum = JurnalUmum::with('code');
        $listJurnalUmum = $listJurnalUmum->orderBy('created_at','desc');
        return DataTables::eloquent($listJurnalUmum)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('add jurnal umum', Auth::user());
        $codes = Code::where('is_parent', 0)
                        ->doesntHave('jurnalUmums')
                        ->get();
        
        $data['title'] = "Tambah Jurnal Umum";
        $data['request'] = $request;
        $data['codes'] = $codes;
        return view('jurnal_umum.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('add jurnal umum', Auth::user());
        try
        {
            // check password
            $check = Hash::check($request->password, Auth::user()->password);
            if (!$check)
            {
                return redirect()->back()->withError("Password yang anda masukkan salah");
            }
            
            // loop every account
            for ($i=0; $i < count($request->code_id) ; $i++) 
            { 
                $nominal = filter_var($request->nominal[$i], FILTER_SANITIZE_NUMBER_INT);

                $jurnalUmumExisting = JurnalUmum::where('code_id',$request->code_id[$i])->first();

                // check if code id is exist
                if(!$jurnalUmumExisting)
                {
                    $jurnalUmum = new JurnalUmum();
                    $jurnalUmum->code_id = $request->code_id[$i];
                    $jurnalUmum->nominal = $nominal;
                    $jurnalUmum->save();
                }

                // call function for create Jurnal
                if($jurnalUmum)
                {
                    JurnalManager::createJurnalUmum($jurnalUmum);
                }
            }

            return redirect()->route('jurnal-umum-list')->withSuccess('Berhasil menambah transaksi');
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
        $this->authorize('edit jurnal umum', Auth::user());
        $jurnalUmum = JurnalUmum::find($id);
        $codes = Code::where('is_parent', 0)
                        ->doesntHave('jurnalUmums')
                        ->orWhere('id', $jurnalUmum->code_id)
                        ->get();

        $data['title'] = "Edit Jurnal Umum";
        $data['codes'] = $codes;
        $data['jurnalUmum'] = $jurnalUmum;
        return view('jurnal_umum.edit', $data);
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
        $this->authorize('edit jurnal umum', Auth::user());
        try
        {
            // check password
            $check = Hash::check($request->password, Auth::user()->password);
            if (!$check)
            {
                return redirect()->back()->withError("Password yang anda masukkan salah");
            }
            
            $nominal = filter_var($request->nominal, FILTER_SANITIZE_NUMBER_INT);

            $jurnalUmum = JurnalUmum::find($id);
            $oldJurnalUmum = $jurnalUmum;

            // check if code id is exist
            if($jurnalUmum)
            {
                $jurnalUmum->code_id = $request->code_id;
                $jurnalUmum->nominal = $nominal;
                
                // update jurnal data
                JurnalManager::updateJurnalUmum($jurnalUmum);

                $jurnalUmum->save();
            }
            
            return redirect()->route('jurnal-umum-list')->withSuccess('Berhasil merubah transaksi');
        }
        catch (\Throwable $th)
        {
            dd($th);
            return redirect()->back()->withError('Gagal menyimpan data');
        }
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
        
        if ($request->kode_anggota)
        {
            $listSimpanan = $listSimpanan->where('kode_anggota', $request->kode_anggota);
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
            DB::transaction(function () use ($request)
            {
                Excel::import(new SimpananImport, $request->file); 
            });
            return redirect()->back()->withSuccess('Import data berhasil');
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
            return redirect()->back()->withError('Gagal import data');
        }
        
    }
}
