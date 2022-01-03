<?php

namespace App\Http\Controllers;

use App\Exports\KartuSimpananExport;
use App\Exports\LaporanExcelExport;
use App\Exports\SimpananExport;
use App\Imports\SimpananImport;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Jurnal;
use App\Models\Penarikan;
use App\Models\Simpanan;
use App\Models\Tabungan;
use App\Models\AngsuranSimpanan;
use App\Models\Code;
use Illuminate\Http\Request;

use App\Managers\JurnalManager;
use App\Managers\SimpananManager;
use App\Models\Company;
use Auth;
use DB;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PDF;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;

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
        $data['unitKerja'] = Company::get()->pluck('nama','id');
        return view('simpanan.index', $data);
    }

    public function indexAjax(Request $request)
    {
        $this->authorize('view simpanan', Auth::user());
        $simpanan = Simpanan::with('anggota')->orderBy('tgl_transaksi','desc');
        if ($request->unit_kerja)
        {
            $simpanan = $simpanan->whereHas('anggota', function ($query) use ($request)
            {
                return $query->where('company_id', $request->unit_kerja);
            });
        }

        if ($request->from || $request->to) {
            if ($request->from) {
                $simpanan = $simpanan->where('tgl_transaksi', '>=', $request->from);
            }
            if ($request->to) {
                $simpanan = $simpanan->where('tgl_transaksi', '<=', $request->to);
            }
        } else {
            $from = Carbon::now()->addDays(-30)->format('Y-m-d');
            $to = Carbon::now()->format('Y-m-d');
            $simpanan = $simpanan->where('tgl_transaksi', '>=', $from)
            ->where('tgl_transaksi', '<=', $to);
        }

        if ($request->jenis_simpanan) {
            $simpanan = $simpanan->where('kode_jenis_simpan', $request->jenis_simpanan);
        }

        if ($request->kode_anggota) {
            $simpanan = $simpanan->where('kode_anggota', $request->kode_anggota);
        }
        if ($request->jenistrans=='A') {
            $simpanan = $simpanan->where('mutasi', 1);
        }else
        if ($request->jenistrans=='T') {
           $simpanan = $simpanan->where('mutasi', 0);
       }else{
           $simpanan = $simpanan;
       }
       $simpanan = $simpanan->orderBy('tgl_transaksi', 'desc');
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
        if ($request->kode_anggota) {
            $data['anggota'] = Anggota::find($request->kode_anggota);
        }
        $bankAccounts = Code::where('CODE', 'like', '102%')->where('is_parent', 0)->get();

        $data['title'] = "Tambah Transaksi Simpanan";
        $data['listJenisSimpanan'] = $listJenisSimpanan;
        $data['request'] = $request;
        $data['bankAccounts'] = $bankAccounts;
        return view('simpanan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('add simpanan', Auth::user());
        try {
            
            // check password
            $check = Hash::check($request->password, Auth::user()->password);
            if (!$check) {
                return redirect()->back()->withError("Password yang anda masukkan salah");
            }

            foreach ($request->jenis_simpanan as $key => $value)
            {
                $besarSimpanan = filter_var($request->besar_simpanan[$key], FILTER_SANITIZE_NUMBER_FLOAT);
                $jenisSimpanan = JenisSimpanan::find($request->jenis_simpanan[$key]);
                $anggotaId = $request->kode_anggota;

                // get next serial number
                $nextSerialNumber = SimpananManager::getSerialNumber(Carbon::now()->format('d-m-Y'));

                if ($jenisSimpanan->nama_simpanan === 'SIMPANAN POKOK') {
                    $checkSimpanan = DB::table('t_simpan')->where('kode_anggota', '=', $anggotaId)->where('kode_jenis_simpan', '=', '411.01.000')->first();

                    if ($checkSimpanan) {
                        $simpananOldValue = $checkSimpanan->besar_simpanan;
                        $simpananCurrentValue = $simpananOldValue + $besarSimpanan;

                        Simpanan::where('kode_anggota', $anggotaId)
                        ->where('kode_jenis_simpan', '411.01.000')
                        ->where('kode_simpan', (int)$checkSimpanan->kode_simpan)
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
                        $angsurSimpanan->tgl_transaksi = Carbon::createFromFormat('d-m-Y', $request->tgl_transaksi[$key]);
                        $angsurSimpanan->created_at = Carbon::now();
                        $angsurSimpanan->updated_at = Carbon::now();
                        $angsurSimpanan->save();

                    } else {
                        $simpanan = new Simpanan();
                        $simpanan->jenis_simpan = strtoupper($jenisSimpanan->nama_simpanan);
                        $simpanan->besar_simpanan = $besarSimpanan;
                        $simpanan->kode_anggota = $anggotaId;
                        $simpanan->u_entry = Auth::user()->name;
                        $simpanan->tgl_entri = Carbon::now();
                        $simpanan->tgl_transaksi = Carbon::createFromFormat('d-m-Y', $request->tgl_transaksi[$key]);
                        $simpanan->periode = Carbon::createFromFormat('d-m-Y', $request->tgl_transaksi[$key]);
                        $simpanan->kode_jenis_simpan = $jenisSimpanan->kode_jenis_simpan;
                        $simpanan->keterangan = ($request->keterangan[$key]) ? $request->keterangan[$key] : null;
                        $simpanan->id_akun_debet = ($request->id_akun_debet[$key]) ? $request->id_akun_debet[$key] : null;
                        $simpanan->serial_number = $nextSerialNumber;
                        $simpanan->save();

                        if ($besarSimpanan < 499999) {
                            $existingSimpanan = DB::table('t_simpan')->where('kode_anggota', '=', $anggotaId)->where('kode_jenis_simpan', '=', '411.01.000')->first();

                            $indexAngsuran = DB::table('t_angsur_simpan')->where('kode_simpan', '=', $existingSimpanan->kode_simpan)->count();

                            $angsurSimpanan = new AngsuranSimpanan();
                            $angsurSimpanan->kode_simpan = $existingSimpanan->kode_simpan;
                            $angsurSimpanan->angsuran_ke = $indexAngsuran + 1;
                            $angsurSimpanan->besar_angsuran = $besarSimpanan;
                            $angsurSimpanan->kode_anggota = $request->kode_anggota;
                            $angsurSimpanan->u_entry = Auth::user()->name;
                            $angsurSimpanan->tgl_entri = Carbon::now();
                            $angsurSimpanan->tgl_transaksi = Carbon::createFromFormat('d-m-Y', $request->tgl_transaksi[$key]);
                            $angsurSimpanan->created_at = Carbon::now();
                            $angsurSimpanan->updated_at = Carbon::now();
                            $angsurSimpanan->save();

                        }
                    }
                } else {

                    $periodeTime = strtotime($request->periode[$key]);

                    $simpanan = new Simpanan();
                    $simpanan->jenis_simpan = strtoupper($jenisSimpanan->nama_simpanan);
                    $simpanan->besar_simpanan = $besarSimpanan;
                    $simpanan->kode_anggota = $anggotaId;
                    $simpanan->u_entry = Auth::user()->name;
                    $simpanan->tgl_entri = Carbon::now();
                    $simpanan->tgl_transaksi = Carbon::createFromFormat('d-m-Y', $request->tgl_transaksi[$key]);
                    if ($request->periode){
                        $simpanan->periode = Carbon::createFromFormat('Y-m-d', $request->periode[$key]);
                    }else{
                        $simpanan->periode = Carbon::createFromFormat('d-m-Y', $request->tgl_transaksi[$key]);
                    }
                    
                    
                    $simpanan->kode_jenis_simpan = $jenisSimpanan->kode_jenis_simpan;
                    $simpanan->keterangan = ($request->keterangan[$key]) ? $request->keterangan[$key] : null;
                    $simpanan->id_akun_debet = ($request->id_akun_debet[$key]) ? $request->id_akun_debet[$key] : null;
                    $simpanan->serial_number = $nextSerialNumber;
                    $simpanan->save();
                }

                JurnalManager::createJurnalSimpanan($simpanan);

                // return redirect()->route('simpanan-list', ['kode_anggota' => $request->kode_anggota])->withSuccess('Berhasil menambah transaksi');
            }
            return redirect()->route('simpanan-list')->withSuccess('Berhasil menambah transaksi');
        } catch (\Throwable $th) {
            Log::error($th->getMessage().'||'.$th->getFile().'||'.$th->getLine());
            return redirect()->back()->withError('Gagal menyimpan data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $simpanan = Simpanan::where('kode_simpan', $request->kode_simpan)->first();
            $besarSimpanan = filter_var($request->besar_simpanan, FILTER_SANITIZE_NUMBER_INT);

            // save simpanan
            $simpanan->updated_by = Auth::user()->id;
            $simpanan->temp_besar_simpanan = $besarSimpanan;
            $simpanan->updated_at = Carbon::now();
            $simpanan->id_status_simpanan = STATUS_SIMPANAN_MENUNGGU_APPROVAL;
            $simpanan->save();

            return redirect()->back()->withSuccess('ubah data simpanan berhasil diajukan');
        } catch (\Throwable $e) {
            dd($e);
            \Log::error($e);
            $message = $e->getMessage();
            return redirect()->back()->withError('gagal mengubah data simpanan');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
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
        return view('simpanan.history', $data);
    }

    public function historyData(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history simpanan', $user);
        if ($user->roles->first()->id == ROLE_ANGGOTA) {
            $anggota = $user->anggota;
            if (is_null($anggota)) {
                return redirect()->back()->withError('Your account has no members');
            }

            $listSimpanan = Simpanan::where('kode_anggota', $anggota->kode_anggota);
        } else {
            $listSimpanan = Simpanan::with('anggota');
        }

        if ($request->from || $request->to) {
            if ($request->from) {
                $listSimpanan = $listSimpanan->where('tgl_transaksi', '>=', $request->from);
            }
            if ($request->to) {
                $listSimpanan = $listSimpanan->where('tgl_transaksi', '<=', $request->to);
            }
        } else {
            $from = Carbon::now()->addDays(-30)->format('Y-m-d');
            $to = Carbon::now()->format('Y-m-d');
            $listSimpanan = $listSimpanan->where('tgl_transaksi', '>=', $from)
            ->where('tgl_transaksi', '<=', $to);
        }
        $listSimpanan = $listSimpanan->orderBy('tgl_transaksi', 'desc');
        return DataTables::eloquent($listSimpanan)->make(true);
    }

    public function paymentValue(Request $request)
    {
        $type = $request->type;
        $anggotaId = $request->anggotaId;
        $attribute = [];

        // Kalkulasi Simpanan Pokok
        if ($type == JENIS_SIMPANAN_POKOK)
        {
            $checkPaymentAvailable = DB::table('t_simpan')
            ->where('kode_anggota', '=', $anggotaId)
            ->where('kode_jenis_simpan', '=', JENIS_SIMPANAN_POKOK)
            ->first();
            $checkPaymentAvailable_old = DB::table('t_tabungan')
            ->where('kode_anggota', '=', $anggotaId)
            ->where('kode_trans', '=', JENIS_SIMPANAN_POKOK)
            ->first();
            $besarSimpananPokok = DB::table('t_jenis_simpan')
            ->where('kode_jenis_simpan', '=', JENIS_SIMPANAN_POKOK)
            ->first();
            if ($checkPaymentAvailable)
            {
                $angsuranList = DB::table('t_angsur_simpan')
                ->where('kode_simpan', '=', $checkPaymentAvailable->kode_simpan)
                ->get();

                $angsuranValue = 0;
                foreach ($angsuranList as $angsuran) {
                    $angsuranValue += $angsuran->besar_angsuran;
                }

                $paymentValue = $besarSimpananPokok->besar_simpanan - $angsuranValue;
                $attribute = $angsuranList;
            }
            elseif ($checkPaymentAvailable_old)
            {
                $paymentValue = 0;
            }
            else
            {
                $paymentValue = $besarSimpananPokok->besar_simpanan;
            }
        } // Kalkulasi Simpanan Wajib
        else if ($type == JENIS_SIMPANAN_WAJIB)
        {
            $latestAngsur = Simpanan::latest('created_at')
            ->where('kode_anggota', $anggotaId)
            ->where('kode_jenis_simpan', JENIS_SIMPANAN_WAJIB)
            ->first();
            $attribute = $latestAngsur;
            $paymentValue=0;
        }
        // Kalkulasi Simpanan Sukarela
        else
        {
            $paymentValue=0;
            $latestAngsur = Simpanan::latest('created_at')
            ->where('kode_anggota', $anggotaId)
            ->where('kode_jenis_simpan', JENIS_SIMPANAN_SUKARELA)
            ->first();
            $attribute = $latestAngsur;
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

        if ($user->roles->first()->id == ROLE_ANGGOTA) {
            $anggota = $user->anggota;
            if (is_null($anggota)) {
                return redirect()->back()->withError('Your account has no members');
            }

            $listSimpanan = Simpanan::where('kode_anggota', $anggota->kode_anggota);
        } else {
            $listSimpanan = Simpanan::with('anggota');
        }

        if ($request->from || $request->to) {
            if ($request->from) {
                $listSimpanan = $listSimpanan->where('tgl_transaksi', '>=', $request->from);
            }
            if ($request->to) {
                $listSimpanan = $listSimpanan->where('tgl_transaksi', '<=', $request->to);
            }
        } else {
            $from = Carbon::now()->addDays(-30)->format('Y-m-d');
            $to = Carbon::now()->format('Y-m-d');
            $listSimpanan = $listSimpanan->where('tgl_transaksi', '>=', $from)
            ->where('tgl_transaksi', '<=', $to);
        }
        if ($request->jenis_simpanan) {
            $listSimpanan = $listSimpanan->where('kode_jenis_simpan', $request->jenis_simpanan);
        }

        if ($request->kode_anggota) {
            $listSimpanan = $listSimpanan->where('kode_anggota', $request->kode_anggota);
        }
        if ($request->jenistrans=='A') {
            $listSimpanan = $listSimpanan->where('mutasi', 1);
        }else
        if ($request->jenistrans=='T') {
           $listSimpanan = $listSimpanan->where('mutasi', 0);
       }else{
           $listSimpanan = $listSimpanan;
       }

        // $listSimpanan = $listSimpanan->get();
       $listSimpanan = $listSimpanan->orderBy('periode', 'desc')->get();

        // share data to view
       view()->share('listSimpanan', $listSimpanan);
       $pdf = PDF::loadView('simpanan.excel', $listSimpanan)->setPaper('a4', 'landscape');

        // download PDF file with download method
       $filename = 'export_simpanan_' . Carbon::now()->format('d M Y') . '.pdf';
       return $pdf->download($filename);
   }

   public function createExcel(Request $request)
   {
    $user = Auth::user();
    $this->authorize('view history simpanan', $user);
    if ($user->roles->first()->id == ROLE_ANGGOTA) {
        $anggota = $user->anggota;
        $request->anggota = $anggota;
    }

    $filename = 'export_simpanan_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
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
    try {

            // DB::transaction(function () use ($request) {
            //     Excel::import(new SimpananImport, $request->file);
            // });

        DB::transaction(function () use ($request)
        {
                // Excel::import(new TransaksiUserImport, $request->file); 
            $collection = (new FastExcel)->import($request->file);
            foreach ($collection as $transaksi) {
                    // dd($transaksi);
                SimpananImport::generatetransaksi($transaksi);
            }
        });
        return redirect()->back()->withSuccess('Import data berhasil');
    } catch (\Throwable $e) {
        \Log::error($e);
        return redirect()->back()->withError('Gagal import data');
    }

}

public function indexCard(Request $request)
{
    try {
        if ($request->kode_anggota) {
            $data['anggota'] = Anggota::find($request->kode_anggota);
        }
        $data['title'] = "Kartu Simpanan";
        $data['request'] = $request;
        return view('simpanan.card.index', $data);
    } catch (\Throwable $e) {
        \Log::error($e);
        return redirect()->back()->withError('Terjadi kesalahan sistem');
    }
}

public function showCard(Request $request,$kodeAnggota)
{
    try {
            // get anggota
        $anggota = Anggota::with('simpanSaldoAwal')->findOrFail($kodeAnggota);

            // get this year
        $thisYear = Carbon::now()->year;
            // $thisYear = 2020;

            // get list simpanan by this year and kode anggota. sort by tgl_entry ascending
        $listSimpanan = Simpanan::whereYear('tgl_transaksi', $thisYear)
        ->where('kode_anggota', $anggota->kode_anggota)
        ->where("mutasi",0)
        ->orderBy('periode', 'asc')
        ->get();

            // data di grouping berdasarkan kode jenis simpan
        $groupedListSimpanan = $listSimpanan->groupBy('kode_jenis_simpan');

            // kode_jenis_simpan yang wajib ada
        $jenisSimpanan = JenisSimpanan::orderBy('sequence', 'asc');
        $requiredKey = $jenisSimpanan->pluck('kode_jenis_simpan');
        $requiredKeyIndex = $jenisSimpanan->pluck('sequence', 'kode_jenis_simpan');

            // set default value untuk key yang tidak ada
        foreach ($requiredKey as $value) {
            if (!isset($groupedListSimpanan[$value])) {
                $groupedListSimpanan[$value] = collect([]);
            }
        }

        $simpananKeys = $groupedListSimpanan->keys();
        $listPengambilan = Penarikan::where('kode_anggota', $anggota->kode_anggota)
        ->whereYear('tgl_transaksi', $thisYear)
        ->whereIn('code_trans', $simpananKeys)
        ->whereraw('paid_by_cashier is not null')
        ->orderBy('tgl_transaksi', 'asc')
        ->get();
            /*
                tiap jenis simpanan di bagi jadi 3 komponen
                1. saldo akhir tahun tiap jenis simpanan
                2. list simpanan untuk tiap jenis simpanan pada tahun ini
                3. jumlah simpanan untuk tiap jenis simpanan pada tahun ini
                4. nama jenis simpanan
                5. total saldo akhir tiap jenis simpanan
            */
                if(!$request->year){
                    $year= Carbon::today()->subYear()->endOfYear();
                }else{
                    $year= Carbon::createFromFormat('Y',$$request->year)->subYear()->endOfYear();
                }
                $listSimpanan = [];
                $index = count($requiredKey);
                foreach ($groupedListSimpanan as $key => $list) {
                    $jenisSimpanan = JenisSimpanan::find($key);
                    if ($jenisSimpanan) {
                        $tabungan = $anggota->simpanSaldoAwal->where('kode_trans', $key)->first();
                        $transsimpan = $anggota->listSimpanan
                                    ->where('kode_jenis_simpan', $key)
                                    ->where('periode','<',$year)
                                    ->where('mutasi',0)
                                    ->sum('besar_simpanan');
                        $transtarik = $anggota->listPenarikan
                                    ->where('code_trans', $key)
                                    ->where('tgl_ambil','<',$year)
                                    ->where('mutasi',0)
                                    ->sum('besar_ambil');
                        $res['name'] = $jenisSimpanan->nama_simpanan;
                        $res['balance'] = ($tabungan) ? $tabungan->besar_tabungan+$transsimpan-$transtarik : 0;
                        $res['list'] = $list;
                        $res['amount'] = $list->sum('besar_simpanan');
                        $res['final_balance'] = $res['balance'] + $res['amount'];
                        $res['withdrawalList'] = $listPengambilan->where('code_trans', $key)->values();
                        $res['withdrawalAmount'] = $listPengambilan->where('code_trans', $key)->values()->sum('besar_ambil');
                        if (isset($requiredKeyIndex[$key])) {
                            $seq = $requiredKeyIndex[$key];
                            $listSimpanan[$seq] = (object)$res;
                        } else {
                            $listSimpanan[$index] = (object)$res;
                            $index++;
                        }
                    }
                }

                $data['anggota'] = $anggota;
                $data['listSimpanan'] = collect($listSimpanan)->sortKeys();
            // dd($data);

                return view('simpanan.card.detail', $data);
            } catch (\Throwable $e) {
                \Log::error($e);
                return redirect()->back()->withError('Terjadi kesalahan sistem');
            }
        }

        public function downloadPDFCard(Request $request,$kodeAnggota)
        {
            try {
            // get anggota
                $anggota = Anggota::with('simpanSaldoAwal')->findOrFail($kodeAnggota);

            // get this year
                $thisYear = Carbon::now()->year;
                $listTabungan = \App\Models\View\ViewSimpanSaldoAwal::where('kode_anggota', $anggota->kode_anggota)
                ->get();
            // get list simpanan by this year and kode anggota. sort by tgl_entry ascending
                $listSimpanan = Simpanan::whereYear('tgl_transaksi', $thisYear)
                ->where('kode_anggota', $anggota->kode_anggota)
                ->where("mutasi",0)
                ->orderBy('periode', 'asc')
                ->get();
            // data di grouping berdasarkan kode jenis simpan
                $groupedListSimpanan = $listSimpanan->groupBy('kode_jenis_simpan');

            // kode_jenis_simpan yang wajib ada
                $jenisSimpanan = JenisSimpanan::orderBy('sequence', 'asc');
                $requiredKey = $jenisSimpanan->pluck('kode_jenis_simpan');
                $requiredKeyIndex = $jenisSimpanan->pluck('sequence', 'kode_jenis_simpan');

            // set default value untuk key yang tidak ada
                foreach ($requiredKey as $value) {
                    if (!isset($groupedListSimpanan[$value])) {
                        $groupedListSimpanan[$value] = collect([]);
                    }
                }

                $simpananKeys = $groupedListSimpanan->keys();
                $listPengambilan = Penarikan::where('kode_anggota', $anggota->kode_anggota)
                ->whereYear('tgl_transaksi', $thisYear)
                ->whereIn('code_trans', $simpananKeys)
                ->whereraw('paid_by_cashier is not null')
                ->orderBy('tgl_transaksi', 'asc')
                ->get();
            /*
                tiap jenis simpanan di bagi jadi 3 komponen
                1. saldo akhir tahun tiap jenis simpanan
                2. list simpanan untuk tiap jenis simpanan pada tahun ini
                3. jumlah simpanan untuk tiap jenis simpanan pada tahun ini
                4. nama jenis simpanan
                5. total saldo akhir tiap jenis simpanan
            */
                if(!$request->year){
                    $year= Carbon::today()->subYear()->endOfYear();
                }else{
                    $year= Carbon::createFromFormat('Y',$$request->year)->subYear()->endOfYear();
                }
                $listSimpanan = [];
                $index = count($requiredKey);
                foreach ($groupedListSimpanan as $key => $list) {
                    $jenisSimpanan = JenisSimpanan::find($key);
                    if ($jenisSimpanan) {
                        $tabungan = $anggota->simpanSaldoAwal->where('kode_trans', $key)->first();
                        $transsimpan = $anggota->listSimpanan
                                    ->where('kode_jenis_simpan', $key)
                                    ->where('periode','<',$year)
                                    ->where('mutasi',0)
                                    ->sum('besar_simpanan');
                        $transtarik = $anggota->listPenarikan
                                    ->where('code_trans', $key)
                                    ->where('tgl_ambil','<',$year)
                                    ->where('mutasi',0)
                                    ->sum('besar_ambil');
                        $res['name'] = $jenisSimpanan->nama_simpanan;
                        $res['balance'] = ($tabungan) ? $tabungan->besar_tabungan+$transsimpan-$transtarik : 0;
                        $res['list'] = $list;
                        $res['amount'] = $list->sum('besar_simpanan');
                        $res['final_balance'] = $res['balance'] + $res['amount'];
                        $res['withdrawalList'] = $listPengambilan->where('code_trans', $key)->values();
                        $res['withdrawalAmount'] = $listPengambilan->where('code_trans', $key)->values()->sum('besar_ambil');
                        if (isset($requiredKeyIndex[$key])) {
                            $seq = $requiredKeyIndex[$key];
                            $listSimpanan[$seq] = (object)$res;
                        } else {
                            $listSimpanan[$index] = (object)$res;
                            $index++;
                        }
                    }
                }

                $data['anggota'] = $anggota;
                $data['listSimpanan'] = collect($listSimpanan)->sortKeys();
            // dd($data);
            // share data to view
                view()->share('data', $data);
                PDF::setOptions(['margin-left' => 0, 'margin-right' => 0]);
                $pdf = PDF::loadView('simpanan.card.export2', $data)->setPaper('a4', 'portrait');

            // download PDF file with download method
                $filename = 'export_kartu_simpanan_' . Carbon::now()->format('d M Y') . '.pdf';
                return $pdf->download($filename);
            } catch (\Throwable $e) {
                \Log::error($e);
                return redirect()->back()->withError('Terjadi kesalahan sistem');
            }
        }

        public function downloadExcelCard($kodeAnggota)
        {
            try {
                $filename = 'export_kartu_simpanan_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
                return Excel::download(new KartuSimpananExport($kodeAnggota), $filename);
            } catch (\Throwable $th) {
                \Log::error($e);
                return redirect()->back()->withError('Terjadi kesalahan sistem');
            }
        }

        public function showJurnal($id)
        {
            try
            {
                $simpanan = Simpanan::find($id);
                if (is_null($simpanan))
                {
                    return response()->json(['message' => 'Not Found'], 404);
                }

                if($simpanan->u_entry!=='System' || $simpanan->u_entry!=='Admin BTB'){

                    if ($simpanan->jurnals->count()==0 && $simpanan->id_status_simpanan==1){
                        if (is_null($simpanan->tgl_transaksi)){
                            $simpanan->tgl_transaksi=$simpanan->tgl_entri;
                            $simpanan->save();
                        }
                        if (is_null($simpanan->serial_number)){
                            $simpanan->serial_number = SimpananManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
                            $simpanan->save();
                        }

                        JurnalManager::createJurnalSimpanan($simpanan);
                    }
                }

                $simpanan2 = Simpanan::find($id);
                $data['simpanan'] = $simpanan2;
                $data['jurnals'] = $simpanan2->jurnals;
                return view('simpanan.jurnal', $data);
            }
            catch (\Throwable $e)
            {
                $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
                Log::error($message);

                return response()->json(['message' => 'Terjadi Kesalahan'], 500);
            }
        }

        public function updateStatusSimpanan(Request $request) {
            try {
                $user = Auth::user();
                $check = Hash::check($request->password, $user->password);
                if (!$check) {
                    Log::error('Wrong Password');
                    return response()->json(['message' => 'Wrong Password'], 412);
                }

                $simpanan = Simpanan::where('kode_simpan', $request->id)->first();

                if ($request->status == STATUS_SIMPANAN_DITERIMA)
                {
                // save simpanan
                    $simpanan->besar_simpanan = $simpanan->temp_besar_simpanan;
                    $simpanan->id_status_simpanan = STATUS_SIMPANAN_DITERIMA;
                    $simpanan->save();

                // update jurnal
                    $journals = $simpanan->jurnals;
                    foreach ($journals as $key => $journal)
                    {
                        if($journal)
                        {
                            $journal->kredit = $simpanan->besar_simpanan;
                            $journal->debet = $simpanan->besar_simpanan;
                            $journal->updated_by = Auth::user()->id;
                            $journal->save();
                        }
                    }
                }
                else if($request->status == STATUS_SIMPANAN_DITOLAK)
                {
                // save simpanan
                    $simpanan->id_status_simpanan = STATUS_SIMPANAN_DITERIMA;
                    $simpanan->save();
                }

                return response()->json(['message' => 'success'], 200);
            } catch (\Exception $e) {
                \Log::error($e);
                $message = $e->getMessage();
                return response()->json(['message' => $message], 500);
            }
        }

        public function laporan(Request $request)
        {
            try
            {
                $years = range(Carbon::now()->year, 2000);
                $data['title'] = 'Laporan Simpanan';
                $data['years'] = $years;
                $data['request'] = $request;
                $data['listJenisSimpanan'] = JenisSimpanan::select('nama_simpanan as name', 'kode_jenis_simpan as id')
                ->take(5)
                ->get();

                $year = $request->tahun;

                if($year)
                {
                    $simpanan = collect(DB::select('SELECT besar_simpanan AS val, month(tgl_transaksi) AS month, kode_jenis_simpan as jenis_simpanan FROM t_simpan WHERE YEAR(tgl_transaksi) = '.$year));
                    $penarikan = collect(DB::select('SELECT besar_ambil AS val, month(tgl_ambil) AS month, code_trans as jenis_simpanan FROM t_pengambilan WHERE YEAR(tgl_ambil) = '.$year));
                    $simpananPerbulan = $simpanan->groupBy('month')
                    ->map(function ($s)
                    {
                        return $s->sum('val');
                    });

                    $penarikanPerbulan = $penarikan->groupBy('month')
                    ->map(function ($p)
                    {
                        return $p->sum('val');
                    });

                    $simpananPerjenis = $simpanan->groupBy('jenis_simpanan')
                    ->map(function ($s, $k)
                    {
                        $perbulan = $s->groupBy('month')
                        ->map(function ($val)
                        {
                            return $val->sum('val');
                        });
                        return collect($perbulan);
                    });

                    $penarikanPerjenis = $penarikan->groupBy('jenis_simpanan')
                    ->map(function ($s, $k)
                    {
                        $perbulan = $s->groupBy('month')
                        ->map(function ($val)
                        {
                            return $val->sum('val');
                        });
                        return collect($perbulan);
                    });

                    $data['simpananPerbulan'] = $simpananPerbulan;
                    $data['simpananPerjenis'] = $simpananPerjenis;
                    $data['penarikanPerbulan'] = $penarikanPerbulan;
                    $data['penarikanPerjenis'] = $penarikanPerjenis;
                }

                return view('simpanan.laporan', $data);
            // return view('simpanan.laporan-excel', $data);
            }
            catch (\Throwable $th)
            {
                $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            // return redirect()->back()->withError($message);
            }
        }

        public function laporanExcel(Request $request)
        {
            $years = range(Carbon::now()->year, 2000);
            $data['years'] = $years;
            $year = $request->tahun;
            $data['listJenisSimpanan'] = JenisSimpanan::select('nama_simpanan as name', 'kode_jenis_simpan as id')
            ->take(5)
            ->get();
            if($year)
            {
                $simpanan = collect(DB::select('SELECT besar_simpanan AS val, month(tgl_transaksi) AS month, kode_jenis_simpan as jenis_simpanan FROM t_simpan WHERE YEAR(tgl_transaksi) = '.$year));
                $penarikan = collect(DB::select('SELECT besar_ambil AS val, month(tgl_ambil) AS month, code_trans as jenis_simpanan FROM t_pengambilan WHERE YEAR(tgl_ambil) = '.$year));
                $simpananPerbulan = $simpanan->groupBy('month')
                ->map(function ($s)
                {
                    return $s->sum('val');
                });

                $penarikanPerbulan = $penarikan->groupBy('month')
                ->map(function ($p)
                {
                    return $p->sum('val');
                });

                $simpananPerjenis = $simpanan->groupBy('jenis_simpanan')
                ->map(function ($s, $k)
                {
                    $perbulan = $s->groupBy('month')
                    ->map(function ($val)
                    {
                        return $val->sum('val');
                    });
                    return collect($perbulan);
                });

                $penarikanPerjenis = $penarikan->groupBy('jenis_simpanan')
                ->map(function ($s, $k)
                {
                    $perbulan = $s->groupBy('month')
                    ->map(function ($val)
                    {
                        return $val->sum('val');
                    });
                    return collect($perbulan);
                });

                $data['simpananPerbulan'] = $simpananPerbulan;
                $data['simpananPerjenis'] = $simpananPerjenis;
                $data['penarikanPerbulan'] = $penarikanPerbulan;
                $data['penarikanPerjenis'] = $penarikanPerjenis;

                $filename = 'laporan_simpanan_' . Carbon::now()->format('d M Y') . '.xlsx';
                return Excel::download(new LaporanExcelExport($data), $filename);
            }
            else
            {
                return redirect()->back()->withError('Link invalid');
            }
        }

        public function delete(Request $request){
           $user = Auth::user();
           $check = Hash::check($request->password, $user->password);
           if (!$check) {
            Log::error('Wrong Password');
            return response()->json(['message' => 'Wrong Password'], 412);
        }
        $this->authorize('delete simpanan', $user);
        try{

         $simpanan = Simpanan::find($request->id);
         if ($simpanan->jurnals->count()>0){
            foreach ($simpanan->jurnals as $jurn){
                $jurn->delete();
            }
        }
        $simpanan ->delete();
        return response()->json(['message' => 'success'], 200);

    }catch (\Throwable $th)
    {
        $message = $th->getMessage().' || '.$th->getFile().' || '.$th->getLine();
        Log::info($message);
        return redirect()->back()->withErrors($message);
    }
}

public function pendingJurnal(Request $request){

   $this->authorize('posting jurnal', Auth::user());
   if(!$request->from)
   {          
    $request->from = Carbon::today()->startOfMonth()->format('d-m-Y');
}
if(!$request->to)
{          
    $request->to = Carbon::today()->endOfMonth()->format('d-m-Y');
}
$startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->startOfDay()->format('Y-m-d');
$endUntilPeriod = Carbon::createFromFormat   ('d-m-Y', $request->to)->endOfDay()->format('Y-m-d');
$trans=Simpanan::
where('mutasi',0) 
->wherenotin('u_entry',['Admin BTB','System']) 
->whereBetween('tgl_transaksi',[ $startUntilPeriod,$endUntilPeriod]) 
->whereDoesntHave('jurnals')->limit(500)
                     // ->toSql();
->get();       

$data['title'] = 'List Pending Jurnal Simpanan';
$data['list'] = $trans;
$data['request'] = $request;

return view('simpanan.jurnalpending', $data);
}

public function postPendingJurnal(Request $request){

   $this->authorize('posting jurnal', Auth::user());
   $kodeSimpan = $request->kode_simpan;
   try{
    foreach ($kodeSimpan as $id){
        $simpanan = Simpanan::find($id);
        $anggota = Anggota::where('kode_anggota',$simpanan->kode_anggota)->first();
        if(!$anggota){
             return redirect()->back()->withErrors('Gagal Poting Anggota '.$simpanan->kode_anggota.' tidak ditemukan di Master Anggota');
        }
        if (is_null($simpanan->serial_number)){
            $simpanan->serial_number = SimpananManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
            $simpanan->save();
        }

        JurnalManager::createJurnalSimpanan($simpanan);
    }
    return redirect()->back()->withSuccess('Posting Berhasil');
}catch (\Throwable $th)
{
    $message = $th->getMessage().' || '.$th->getFile().' || '.$th->getLine();
    Log::info($message);
    return redirect()->back()->withErrors($message);
}

}

}
