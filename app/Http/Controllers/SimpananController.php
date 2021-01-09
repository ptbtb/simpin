<?php

namespace App\Http\Controllers;

use App\Exports\KartuSimpananExport;
use App\Exports\SimpananExport;
use App\Imports\SimpananImport;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\Simpanan;
use App\Models\Tabungan;
use App\Models\AngsuranSimpanan;
use Illuminate\Http\Request;

use Auth;
use DB;
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

        if ($request->kode_anggota)
        {
            $simpanan = $simpanan->where('kode_anggota', $request->kode_anggota);
        }
        $simpanan = $simpanan->orderBy('tgl_entri','desc');
        return DataTables::eloquent($simpanan)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('add simpanan', Auth::user());
        $listJenisSimpanan = JenisSimpanan::all(); 
        if ($request->kode_anggota)
        {
            $data['anggota'] = Anggota::find($request->kode_anggota);
        }
        $data['title'] = "Tambah Transaksi Simpanan";
        $data['listJenisSimpanan'] = $listJenisSimpanan;
        $data['request'] = $request;
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
            $anggotaId = $request->kode_anggota;
            
            if($jenisSimpanan->nama_simpanan === 'SIMPANAN POKOK') {
                $checkSimpanan = DB::table('t_simpan')->where('kode_anggota', '=', $anggotaId)->where('kode_jenis_simpan', '=', '411.01.000')->first();
                
                if ($checkSimpanan) {
                    $simpananOldValue = $checkSimpanan->besar_simpanan;
                    $simpananCurrentValue = $simpananOldValue + $besarSimpanan;

                    Simpanan::where('kode_anggota', $anggotaId)
                            ->where('kode_jenis_simpan', '411.01.000')
                            ->where('kode_simpan', (int) $checkSimpanan->kode_simpan)
                            ->update([
                                'besar_simpanan' => $simpananCurrentValue,
                                'updated_at' => Carbon::now()
                            ]);
                    
                    $indexAngsuran = DB::table('t_angsur_simpan')->where('kode_simpan', '=', $checkSimpanan->kode_simpan)->count();

                    $angsurSimpanan = new AngsuranSimpanan();
                    $angsurSimpanan->kode_simpan = $checkSimpanan->kode_simpan;
                    $angsurSimpanan->angsuran_ke = $indexAngsuran + 1;
                    $angsurSimpanan->besar_angsuran = $besarSimpanan;
                    $angsurSimpanan->kode_anggota = $request->kode_anggota;
                    $angsurSimpanan->u_entry = Auth::user()->name;
                    $angsurSimpanan->tgl_entri = Carbon::now();
                    $angsurSimpanan->created_at = Carbon::now();
                    $angsurSimpanan->updated_at = Carbon::now();
                    $angsurSimpanan->save();

                }
                else {
                    $simpanan = new Simpanan();
                    $simpanan->jenis_simpan = strtoupper($jenisSimpanan->nama_simpanan);
                    $simpanan->besar_simpanan = $besarSimpanan;
                    $simpanan->kode_anggota = $anggotaId;
                    $simpanan->u_entry = Auth::user()->name;
                    $simpanan->tgl_entri = Carbon::now();
                    $simpanan->kode_jenis_simpan = $jenisSimpanan->kode_jenis_simpan;
                    $simpanan->keterangan = ($request->keterangan)? $request->keterangan:null;
                    $simpanan->save();

                    if ($besarSimpanan < 499999){
                        $existingSimpanan = DB::table('t_simpan')->where('kode_anggota', '=', $anggotaId)->where('kode_jenis_simpan', '=', '411.01.000')->first();
                        
                        $indexAngsuran = DB::table('t_angsur_simpan')->where('kode_simpan', '=', $existingSimpanan->kode_simpan)->count();

                        $angsurSimpanan = new AngsuranSimpanan();
                        $angsurSimpanan->kode_simpan = $existingSimpanan->kode_simpan;
                        $angsurSimpanan->angsuran_ke = $indexAngsuran + 1;
                        $angsurSimpanan->besar_angsuran = $besarSimpanan;
                        $angsurSimpanan->kode_anggota = $request->kode_anggota;
                        $angsurSimpanan->u_entry = Auth::user()->name;
                        $angsurSimpanan->tgl_entri = Carbon::now();
                        $angsurSimpanan->created_at = Carbon::now();
                        $angsurSimpanan->updated_at = Carbon::now();
                        $angsurSimpanan->save();
                        
                    }
                }
            }
            else {

                $periodeTime = strtotime($request->periode);
                
                $simpanan = new Simpanan();
                $simpanan->jenis_simpan = strtoupper($jenisSimpanan->nama_simpanan);
                $simpanan->besar_simpanan = $besarSimpanan;
                $simpanan->kode_anggota = $anggotaId;
                $simpanan->u_entry = Auth::user()->name;
                $simpanan->tgl_entri = Carbon::now();
                $simpanan->periode = date("Y-m-d", $periodeTime);
                $simpanan->kode_jenis_simpan = $jenisSimpanan->kode_jenis_simpan;
                $simpanan->keterangan = ($request->keterangan)? $request->keterangan:null;
                $simpanan->save();
            }

            // return redirect()->route('simpanan-list', ['kode_anggota' => $request->kode_anggota])->withSuccess('Berhasil menambah transaksi');
            return redirect()->route('simpanan-list')->withSuccess('Berhasil menambah transaksi');
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

    public function paymentValue(Request $request){
        $type = $request->type;
        $anggotaId = $request->anggotaId;
        $attribute = [];

        // Kalkulasi Simpanan Pokok
        if ($type == '411.01.000') {
            $checkPaymentAvailable = DB::table('t_simpan')->where('kode_anggota', '=', $anggotaId)->where('kode_jenis_simpan', '=', '411.01.000')->first();
            $checkPaymentAvailable_old = DB::table('t_tabungan')->where('kode_anggota', '=', $anggotaId)->where('kode_trans', '=', '411.01.000')->first();
            $besarSimpananPokok = DB::table('t_jenis_simpan')->where('kode_jenis_simpan', '=', '411.01.000')->first();
            if ($checkPaymentAvailable) {
                $angsuranList = DB::table('t_angsur_simpan')->where('kode_simpan', '=', $checkPaymentAvailable->kode_simpan)->get();

                $angsuranValue = 0;
                foreach($angsuranList as $angsuran){
                    $angsuranValue += $angsuran->besar_angsuran;
                }

                $paymentValue = $besarSimpananPokok->besar_simpanan - $angsuranValue;
                $attribute = $angsuranList;
            }elseif($checkPaymentAvailable_old){
                $paymentValue = 0;
            }
            else {
                $paymentValue = $besarSimpananPokok->besar_simpanan;
            }
        }

        // Kalkulasi Simpanan Wajib
        else if($type == '411.12.000') {
            
            $payment = DB::table('t_anggota')
                    ->join('t_penghasilan', 't_anggota.kode_anggota', 't_penghasilan.kode_anggota')
                    ->join('t_kelas_company', 't_penghasilan.kelas_company_id', 't_kelas_company.id')
                    ->join('t_kelas_simpanan', 't_kelas_company.id', 't_kelas_simpanan.kelas_company_id')
                    ->select('t_kelas_simpanan.simpanan as paymentValue')
                    ->where('t_anggota.kode_anggota', '=', $anggotaId)
                    ->first();
            
            $latestAngsur = Simpanan::latest('created_at')->where('kode_anggota', $anggotaId)->where('kode_jenis_simpan', '411.12.000')->first();
            $attribute = $latestAngsur;
            
            $paymentValue = $payment->paymentValue;
        }

        // Kalkulasi Simpanan Sukarela
        else {

            $anggota = DB::table('t_anggota')
                    ->join('t_penghasilan', 't_anggota.kode_anggota', 't_penghasilan.kode_anggota')
                    ->select('t_penghasilan.gaji_bulanan as penghasilan')
                    ->where('t_anggota.kode_anggota', '=', $anggotaId)
                    ->first();
            $latestAngsur = Simpanan::latest('created_at')->where('kode_anggota', $anggotaId)->where('kode_jenis_simpan', '502.01.000')->first();
            $attribute = $latestAngsur;
            if ($latestAngsur){
                $paymentValue= $latestAngsur->besar_simpanan;
            }else{
            $paymentValue = 0.65 * $anggota->penghasilan;
            }

        }
        
        return response()->json([
            'paymentValue' => $paymentValue,
            'attribute' => $attribute
        ], 200);
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

    public function indexCard(Request $request)
    {
        try
        {
            if ($request->kode_anggota)
            {
                $data['anggota'] = Anggota::find($request->kode_anggota);
            }
            $data['title'] = "Kartu Simpanan";
            $data['request'] = $request;
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
            // $thisYear = Carbon::now()->year;
            $thisYear = 2020;

            // get list simpanan by this year and kode anggota. sort by tgl_entry ascending
            $listSimpanan = Simpanan::whereYear('tgl_entri', $thisYear)
                                    ->where('kode_anggota', $anggota->kode_anggota)
                                    ->orderBy('tgl_entri','asc')
                                    ->get();
                                    
            // data di grouping berdasarkan kode jenis simpan
            $groupedListSimpanan = $listSimpanan->groupBy('kode_jenis_simpan');

            // kode_jenis_simpan yang wajib ada
            $jenisSimpanan = JenisSimpanan::orderBy('sequence', 'asc');
            $requiredKey = $jenisSimpanan->pluck('kode_jenis_simpan');
            $requiredKeyIndex = $jenisSimpanan->pluck('sequence','kode_jenis_simpan');

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
                    if (isset($requiredKeyIndex[$key]))
                    {
                        $seq = $requiredKeyIndex[$key];
                        $listSimpanan[$seq] = (object)$res;
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
            $anggota = Anggota::with('tabungan')->findOrFail($kodeAnggota);

            // get this year
            $thisYear = Carbon::now()->year;
            $listTabungan = Tabungan::where('kode_anggota', $anggota->kode_anggota)
                                    ->get();
            // get list simpanan by this year and kode anggota. sort by tgl_entry ascending
            $listSimpanan = Simpanan::whereYear('tgl_entri', $thisYear)
                                    ->where('kode_anggota', $anggota->kode_anggota)
                                    ->orderBy('tgl_entri','asc')
                                    ->get();                  
            // data di grouping berdasarkan kode jenis simpan
            $groupedListSimpanan = $listSimpanan->groupBy('kode_jenis_simpan');

            // kode_jenis_simpan yang wajib ada
            $jenisSimpanan = JenisSimpanan::orderBy('sequence', 'asc')->take(3);
            $requiredKey = $jenisSimpanan->pluck('kode_jenis_simpan');
            $requiredKeyIndex = $jenisSimpanan->pluck('sequence','kode_jenis_simpan');

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
                    $res['balance'] = $listTabungan->where('kode_trans',$key)->values()->sum('besar_tabungan');
                    $res['list'] = $list;
                    $res['amount'] = $list->sum('besar_simpanan');
                    $res['final_balance'] = $res['balance'] + $res['amount'];
                    $res['withdrawalList'] = $listPengambilan->where('code_trans', $key)->values();
                    $res['withdrawalAmount'] = $listPengambilan->where('code_trans', $key)->values()->sum('besar_ambil');
                    if (isset($requiredKeyIndex[$key]))
                    {
                        $seq = $requiredKeyIndex[$key];
                        $listSimpanan[$seq] = (object)$res;
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
