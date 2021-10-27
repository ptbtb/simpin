<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Code;
use App\Models\SaldoAwal;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use App\Managers\JurnalManager;

use App\Exports\SaldoAwalExport;

use App\Imports\SaldoAwalImport;

use Auth;
use DB;
use Hash;
use Carbon\Carbon;
use Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class SaldoAwalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view saldo awal', Auth::user());
        $listSaldoAwal = SaldoAwal::with('code');
        $listSaldoAwal = $listSaldoAwal->orderBy('created_at','desc');

        $data['title'] = "List Saldo Awal";
        $data['request'] = $request;
        $data['listSaldoAwal'] = $listSaldoAwal;

        return view('saldo_awal.index', $data);
    }

    public function indexAjax(Request $request)
    {
        $this->authorize('view saldo awal', Auth::user());
        $listSaldoAwal = SaldoAwal::with('code');
        $listSaldoAwal = $listSaldoAwal->orderBy('created_at','desc');
        return DataTables::eloquent($listSaldoAwal)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('add saldo awal', Auth::user());
        $codes = Code::where('is_parent', 0)
                        ->where('CODE', 'not like', "411%")
                        ->where('CODE', 'not like', "106%")
                        ->where('CODE', 'not like', "502%")
                        ->where('CODE', 'not like', "105%")
                        ->doesntHave('saldoAwals')
                        ->get();
        
        $data['title'] = "Tambah Saldo Awal";
        $data['request'] = $request;
        $data['codes'] = $codes;
        return view('saldo_awal.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('add saldo awal', Auth::user());
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
                $filterNominal = filter_var($request->nominal[$i], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $nominal = str_replace(",", ".", $filterNominal);

                $saldoAwalExisting = SaldoAwal::where('code_id',$request->code_id[$i])->first();

                // check if code id is exist
                if(!$saldoAwalExisting)
                {
                    $saldoAwal = new SaldoAwal();
                    $saldoAwal->code_id = $request->code_id[$i];
                    $saldoAwal->nominal = $nominal;
                    $saldoAwal->created_at = Carbon::today()->subYear()->endOfYear()->format('Y-m-d');
                    $saldoAwal->save();
                }

                // call function for create Jurnal
                if($saldoAwal)
                {
                    JurnalManager::createSaldoAwal($saldoAwal);
                }
            }

            return redirect()->route('saldo-awal-list')->withSuccess('Berhasil menambah transaksi');
        }
        catch (\Throwable $th)
        {
            \Log::error($th);
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
        $this->authorize('edit saldo awal', Auth::user());
        $saldoAwal = SaldoAwal::find($id);
        $codes = Code::where('is_parent', 0)
                        ->where('CODE', 'not like', "411%")
                        ->where('CODE', 'not like', "106%")
                        ->where('CODE', 'not like', "502%")
                        ->where('CODE', 'not like', "105%")
                        ->doesntHave('saldoAwals')
                        ->orWhere('id', $saldoAwal->code_id)
                        ->get();

        $data['title'] = "Edit Saldo Awal";
        $data['codes'] = $codes;
        $data['saldoAwal'] = $saldoAwal;
        return view('saldo_awal.edit', $data);
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
        $this->authorize('edit saldo awal', Auth::user());
        try
        {
            // check password
            $check = Hash::check($request->password, Auth::user()->password);
            if (!$check)
            {
                return redirect()->back()->withError("Password yang anda masukkan salah");
            }
            
            $filterNominal = filter_var($request->nominal, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
            $nominal = str_replace(",", ".", $filterNominal);

            $saldoAwal = SaldoAwal::find($id);
            $oldSaldoAwal = $saldoAwal;

            // check if code id is exist
            if($saldoAwal)
            {
                $saldoAwal->code_id = $request->code_id;
                $saldoAwal->nominal = $nominal;
                
                // update jurnal data
                JurnalManager::updateSaldoAwal($saldoAwal);

                $saldoAwal->save();
            }
            
            return redirect()->route('saldo-awal-list')->withSuccess('Berhasil merubah transaksi');
        }
        catch (\Throwable $th)
        {
            \Log::error($th);
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

    public function importExcel()
    {
        $this->authorize('add saldo awal', Auth::user());
        $data['title'] = 'Import Saldo Awal';
        return view('saldo_awal.import', $data);
    }

    public function storeImportExcel(Request $request)
    {
        $this->authorize('import saldo awal', Auth::user());
        try
        {
            DB::transaction(function () use ($request)
            {
                Excel::import(new SaldoAwalImport, $request->file); 
            });
            return redirect()->back()->withSuccess('Import data berhasil');
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
            return redirect()->back()->withError('Gagal import data');
        }
        
    }

    public function createExcel(Request $request) {
        $user = Auth::user();
        $this->authorize('view saldo awal', $user);

        $filename = 'export_saldo_awal_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        return Excel::download(new SaldoAwalExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
}
