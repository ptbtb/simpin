<?php

namespace App\Http\Controllers;

use App\Events\Penarikan\PenarikanApproved;
use Illuminate\Http\Request;

use App\Events\Penarikan\PenarikanCreated;
use App\Events\Penarikan\PenarikanUpdated;
use App\Exports\ListPenarikanExport;
use App\Exports\PenarikanExport;
use App\Imports\PenarikanImport;
use App\Managers\JurnalManager;
use App\Managers\PenarikanManager;
use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Penarikan;
use App\Models\Simpanan;
use App\Models\Tabungan;
use App\Models\Code;
use App\Models\JkkPrinted;
use App\Models\Pinjaman;
use App\Models\SimpinRule;
use App\Models\View\ViewSaldo;
use App\Models\StatusPenarikan;
use Carbon\Carbon;
use DB;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class PenarikanController extends Controller
{
    public function create()
    {
        try {
            $user = Auth::user();
            $this->authorize('add penarikan', $user);

            $data['title'] = "Buat Penarikan";
            $data['jenisSimpanan'] = JenisSimpanan::where('is_normal_withdraw', 1)->get();
            return view('penarikan.create', $data);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (isset($e->errorInfo[2])) {
                $message = $e->errorInfo[2];
            }
            return redirect()->back()->withError($message);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $this->authorize('add penarikan', $user);

            // check password
            $check = Hash::check($request->password, $user->password);
            if (!$check) {
                return redirect()->back()->withError("Password yang anda masukkan salah");
            }

            $anggota = Anggota::with('tabungan')->find($request->kode_anggota);

            // check max penarikan user
            $thisYear = Carbon::now()->year;
            $penarikanUser = Penarikan::approved()
                                        ->where('kode_anggota', $anggota->kode_anggota)
                                        ->whereYear('created_at', $thisYear)
                                        ->get();

            $simpinRule = SimpinRule::findOrFail(SIMPIN_RULE_MAX_PENGAMBILAN_DALAM_SETAHUN);

            if ($penarikanUser->count() >= $simpinRule->value)
            {
                return redirect()->back()->withError('Gagal melakukan penarikan. Jumlah penarikan anda tahun ini adalah '. $penarikanUser->count() .'.Maksimal penarikan dalam setahun adalah '. $simpinRule->value);
            }

            $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                                    ->notPaid()
                                    ->japan()
                                    ->get();

            $tenor1 = $listPinjaman->whereIn('lama_angsuran', [36,48,60,72])
                                    ->values();
            $tenor2 = $listPinjaman->whereIn('lama_angsuran', [10, 20, 30])
                                    ->values();

            foreach ($request->jenis_simpanan as $kode)
            {
                $jenissimpanan = JenisSimpanan::where('kode_jenis_simpan', $kode)->first();
                $tabungan = $anggota->tabungan->where('kode_trans', $kode)->first();
                $besarPenarikan = filter_var($request->besar_penarikan[$kode], FILTER_SANITIZE_NUMBER_INT);
                $maxtarik = $tabungan->totalBesarTabungan * $jenissimpanan->max_withdraw;
                
                if (is_null($tabungan))
                {
                    return redirect()->back()->withError($anggota->nama_anggota . " belum memiliki tabungan");
                }
                else if ($tabungan->totalBesarTabungan < $besarPenarikan)
                {
                    return redirect()->back()->withError("Saldo tabungan tidak mencukupi");
                }
                else if($tenor1->count())
                {
                    $sisaPinjaman = $tenor1->sum('sisa_pinjaman');
                    $minSaldo = 1/5*$sisaPinjaman;
                    if ($tabungan->besar_tabungan < $minSaldo)
                    {
                        return redirect()->back()->withError("Saldo tabungan tidak mencukupi. Minimal saldo yang tersisa harus lebih dari Rp ". number_format($minSaldo, 0, ',', '.'));
                    }
                }
                else if($tenor2->count())
                {
                    $sisaPinjaman = $tenor2->sum('sisa_pinjaman');
                    $minSaldo = 1/8*$sisaPinjaman;
                    if ($tabungan->besar_tabungan < $minSaldo)
                    {
                        return redirect()->back()->withError("Saldo tabungan tidak mencukupi. Minimal saldo yang tersisa harus lebih dari Rp ". number_format($minSaldo, 0, ',', '.'));
                    }
                }
                else if ($besarPenarikan > $maxtarik + 1)
                {
                    return redirect()->back()->withError("Penarikan simpanan " . $jenissimpanan->nama_simpanan . " tidak boleh melebihi ".$jenissimpanan->max_withdraw." dari saldo tabungan");
                }
            }

            foreach ($request->jenis_simpanan as $kode)
            {
                $penarikan = new Penarikan();
                // get next serial number
                $nextSerialNumber = PenarikanManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
                $tabungan = $anggota->tabungan->where('kode_trans', $kode)->first();
                $besarPenarikan = filter_var($request->besar_penarikan[$kode], FILTER_SANITIZE_NUMBER_INT);

                DB::transaction(function () use ($besarPenarikan, $anggota, $tabungan, &$penarikan, $user, $nextSerialNumber) {
                    $penarikan->kode_anggota = $anggota->kode_anggota;
                    $penarikan->kode_tabungan = $tabungan->kode_tabungan;
                    $penarikan->id_tabungan = $tabungan->id;
                    $penarikan->besar_ambil = $besarPenarikan;
                    $penarikan->code_trans = $tabungan->kode_trans;
                    $penarikan->tgl_ambil = Carbon::now();
                    $penarikan->u_entry = $user->name;
                    $penarikan->created_by = $user->id;
                    $penarikan->status_pengambilan = STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI;
                    $penarikan->serial_number = $nextSerialNumber;
                    $penarikan->save();
                });

                event(new PenarikanCreated($penarikan, $tabungan));
            }
            // return redirect()->route('penarikan-receipt', ['id' => $penarikan->kode_ambil])->withSuccess("Penarikan berhasil");
            return redirect()->back()->withSuccess('Permintaan penarikan berhasil disimpan dan dalam proses persetujuan');
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (isset($e->errorInfo[2])) {
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
        view()->share('penarikan', $penarikan);
        $pdf = PDF::loadView('penarikan.receiptpdf', $penarikan)->setPaper('a4', 'portrait');

        // download PDF file with download method
        $filename = 'receipt_penarikan_' . $penarikan->anggota->nama_anggota . "_" . Carbon::now()->format('d M Y') . '.pdf';
        return $pdf->download($filename);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history penarikan', $user);

        $listPenarikan = Penarikan::with('anggota')->whereraw('paid_by_cashier is not null')->orderBy('tgl_ambil','desc');

        if ($request->kode_anggota) {
            $listPenarikan = $listPenarikan->where('kode_anggota', $request->kode_anggota);
        }

        if ($request->from) {
            $listPenarikan = $listPenarikan->where('tgl_ambil', '>=', $request->from);
        }
        if ($request->to) {
            $listPenarikan = $listPenarikan->where('tgl_ambil', '<=', $request->to);
        }
        if ($user->isAnggota()) {
            $listPenarikan = $listPenarikan->where('kode_anggota', $user->anggota->kode_anggota);
        }

        $listPenarikan = $listPenarikan->orderBy('tgl_ambil', 'desc')
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

        if ($request->kode_anggota) {
            $listPenarikan = $listPenarikan->where('kode_anggota', $request->kode_anggota);
        }

        if ($request->from) {
            $listPenarikan = $listPenarikan->where('tgl_ambil', '>=', $request->from);
        }
        if ($request->to) {
            $listPenarikan = $listPenarikan->where('tgl_ambil', '<=', $request->to);
        }

        $listPenarikan = $listPenarikan->orderBy('tgl_ambil', 'desc')
            ->has('anggota')
            ->get();

        // share data to view
        view()->share('listPenarikan', $listPenarikan);
        $pdf = PDF::loadView('penarikan.excel', $listPenarikan)->setPaper('a4', 'landscape');

        // download PDF file with download method
        $filename = 'export_history_penarikan_' . Carbon::now()->format('d M Y') . '.pdf';
        return $pdf->download($filename);
    }

    public function createExcel(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history penarikan', $user);
        $filename = 'export_transaksi_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
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
        try {
            Excel::import(new PenarikanImport, $request->file);
            return redirect()->back()->withSuccess('Import data berhasil');
        } catch (\Throwable $e) {
            \Log::error($e);
            return redirect()->back()->withError('Gagal import data');
        }
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->isAnggota()) {
                $anggota = $user->anggota;
                if (is_null($anggota)) {
                    return redirect()->back()->withError('Your account has no members');
                }

                $listPenarikan = Penarikan::where('kode_anggota', $anggota->kode_anggota)->orderBy('tgl_ambil','desc')
                    ->get();
            } else {
                $listPenarikan = Penarikan::with('anggota')->orderBy('tgl_ambil','desc')->get();
            }

            $bankAccounts = Code::where('CODE', 'like', '102%')->where('is_parent', 0)->get();

            $statusPenarikans = StatusPenarikan::get();

            $anggotas = Anggota::get();

            $data['title'] = "List Penarikan Simpanan";
            $data['listPenarikan'] = $listPenarikan;
            $data['request'] = $request;
            $data['bankAccounts'] = $bankAccounts;
            $data['statusPenarikans'] = $statusPenarikans;
            $data['anggotas'] = $anggotas;
            return view('penarikan.index', $data);
        } catch (\Throwable $e) {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    /**
     * Display a listing of the resource through ajax.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAjax(Request $request)
    {

        try {
            $user = Auth::user();

            $listPenarikan = Penarikan::with('anggota', 'tabungan', 'statusPenarikan', 'createdBy', 'approvedBy', 'paidByCashier', 'jurnals', 'akunDebet')
                                        ->where('is_pelunasan_dipercepat', 0);

            if($request->status_penarikan != "")
            {
                $listPenarikan->where('status_pengambilan', $request->status_penarikan);
            }else{
                $listPenarikan->where('status_pengambilan', $request->status_penarikan);
            }

            if($request->tgl_ambil != "")
            {
                $tgl_ambil = Carbon::createFromFormat('d-m-Y', $request->tgl_ambil)->toDateString();

                $listPenarikan->where('tgl_ambil', $tgl_ambil);
            }

            if($request->anggota != "")
            {
                $listPenarikan->where('kode_anggota', $request->anggota);
            }

            if ($user->isAnggota())
            {
                $anggota = $user->anggota;

                $listPenarikan->where('kode_anggota', $anggota->kode_anggota);
            }

            $bankAccounts = Code::where('CODE', 'like', '102%')->where('is_parent', 0)->get();

            $jenisSimpanan = JenisSimpanan::all();

            return Datatables::eloquent($listPenarikan)
                                ->editColumn('tgl_ambil', function ($request) {
                                    if($request->tgl_ambil)
                                    {
                                        return $request->tgl_ambil->format('d M Y');
                                    }
                                })
                                ->editColumn('besar_ambil', function ($request) {
                                    return "Rp ". number_format($request->besar_ambil,0,",",".");
                                })
                                ->editColumn('tgl_acc', function ($request) {
                                    if($request->tgl_acc)
                                    {
                                        return $request->tgl_acc->format('d M Y');
                                    }
                                })
                                ->editColumn('jenis_simpanan', function ($request) use($jenisSimpanan){
                                    return strtoupper($jenisSimpanan->where('kode_jenis_simpan', $request->code_trans)->first()->nama_simpanan);
                                })
                                ->addIndexColumn()
                                ->make(true);

        }
        catch (\Throwable $e)
        {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function updateStatus(Request $request)
    {
        try {

            $user = Auth::user();
            $check = Hash::check($request->password, $user->password);
            if (!$check) {
                Log::error('Wrong Password');
                return response()->json(['message' => 'Wrong Password'], 412);
            }

            // get kode ambil's data when got from check boxes
            if (isset($request->kode_ambil_ids))
            {
                $kodeAmbilIds = json_decode($request->kode_ambil_ids);
            }
             \Log::info($request);
            foreach ($kodeAmbilIds as $key => $kodeAmbilId)
            {
                $penarikan = Penarikan::find($kodeAmbilId);
                \Log::info($penarikan->status_pengambilan);
                \Log::info($request->old_status);

                // check penarikan's status must same as old_status
                if($penarikan->status_pengambilan == $request->old_status)
                {

                    if ($request->status == STATUS_PENGAMBILAN_DIBATALKAN)
                    {
                        $penarikan->status_pengambilan = STATUS_PENGAMBILAN_DIBATALKAN;
                        $penarikan->save();
                        return response()->json(['message' => 'success'], 200);
                    }


                    if (is_null($penarikan)) {
                        return response()->json(['message' => 'not found'], 404);
                    }

                    if ($request->status == STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_BENDAHARA) {
                        $this->authorize('approve penarikan', $user);
                        $statusPenarikanSekarang = $penarikan->statusPenarikan;
                        if ($penarikan->besar_pinjam <= $statusPenarikanSekarang->batas_pengajuan) {
                            $penarikan->status_pengambilan = STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN;
                        } else {
                            $penarikan->status_pengambilan = $request->status;
                        }
                    } elseif ($request->status == STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_KETUA) {
                        $this->authorize('approve penarikan', $user);
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
                        $this->authorize('bayar pengajuan pinjaman', $user);
                        Log::info($request->status);
                        $penarikan->paid_by_cashier = $user->id;
                        $penarikan->tgl_transaksi = $request->tgl_transaksi;
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

                        $penarikan->id_akun_debet = ($request->id_akun_debet) ? $request->id_akun_debet : null;
                    }

                    if ($request->keterangan)
                    {
                        $penarikan->description = $request->keterangan;
                    }

                    $penarikan->save();
                    if ($penarikan->menungguPembayaran()) {
                        event(new PenarikanApproved($penarikan));
                    } elseif ($penarikan->diterima()) {
                        JurnalManager::createJurnalPenarikan($penarikan);
                    }
                    event(new PenarikanUpdated($penarikan));
                }
            }

            return response()->json(['message' => 'success'], 200);
        } catch (\Exception $e) {
            $message = class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            return response()->json(['message' => $message], 500);
        }
    }

    public function printJkk()
    {
        try {
            $listPenarikan = Penarikan::needPrintJkk()
                ->menungguPembayaran()
                ->get();

            $data['title'] = "Print JKK";
            $data['listPenarikan'] = $listPenarikan;
            return view('penarikan.printJKK', $data);
        } catch (\Throwable $e) {
            Log::error($e);
            $message = $e->getMessage();
            return response()->json(['message' => $message], 500);
        }
    }

    public function storePrintJkk(Request $request)
    {
        try {
            $listPenarikan = Penarikan::whereIn('kode_ambil', $request->kode_ambil)
                ->get();

            foreach ($listPenarikan as $penarikan) {
                $penarikan->no_jkk = $request->no_jkk;
                $penarikan->status_jkk = 1;
                $penarikan->save();
            }

            // create jkkprinted
            $jkkPrinted = JkkPrinted::where('jkk_number',  $request->no_jkk)->first();
            if (is_null($jkkPrinted))
            {
                $jkkPrinted = new JkkPrinted();
                $jkkPrinted->jkk_number = $request->no_jkk;
                $jkkPrinted->jkk_printed_type_id = JKK_PRINTED_TYPE_PENARIKAN_SIMPANAN;
                $jkkPrinted->printed_at = Carbon::createFromFormat('Y-m-d', $request->tgl_print);
                $jkkPrinted->printed_by = Auth::user()->id;
                $jkkPrinted->save();
            }

            $data['tgl_print']=Carbon::createFromFormat('Y-m-d', $request->tgl_print);

            $data['listPenarikan'] = $listPenarikan;
            $data['jenisSimpanan'] = JenisSimpanan::all();
            view()->share('data', $data);
            PDF::setOptions(['margin-left' => 0, 'margin-right' => 0]);
            $pdf = PDF::loadView('penarikan.pdfJKK', $data)->setPaper('a4', 'landscape');

            // download PDF file with download method
            $filename = $request->no_jkk . '-' . $data['tgl_print'] . '.pdf';
            return $pdf->download($filename);

            // return view('penarikan.pdfJKK', $data);
        } catch (\Throwable $e) {
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

    public function viewDataJurnalPenarikan($id)
    {
        $penarikan = Penarikan::find($id);
        $data['penarikan'] = $penarikan;
        return view('penarikan.viewJurnal', $data);
    }

    public function showFormKeluarAnggota()
    {
        $user = Auth::user();
        $this->authorize('penarikan keluar anggota', $user);

        $data['title'] = "Keluar Anggota";
        return view('penarikan.keluar_anggota', $data);
    }

    public function storeFormKeluarAnggota(Request $request)
    {
        try
        {
            $user = Auth::user();
            $this->authorize('penarikan keluar anggota', $user);

            $saldo = ViewSaldo::where('kode_anggota', $request->kode_anggota)->first();
            $pinjaman = Pinjaman::where('kode_anggota', $request->kode_anggota)->get();
            $kalkulasiPinjaman = $saldo->jumlah - ($pinjaman->sum('sisa_pinjaman') + $pinjaman->sum('jasa_pelunasan_dipercepat'));
            if ($kalkulasiPinjaman < 0)
            {
                $message = 'Tidak bisa keluar anggota. Saldo simpanan tidak mencukupi untuk membayar sisa pinjaman. Silahkan lunasi pinjaman anda terlebih dahulu';
                return redirect()->back()->withError($message);
            }

            $query = 'SELECT SUM(besar_simpanan)AS besar_simpanan, kode_jenis_simpan FROM t_simpan WHERE kode_anggota = '.$request->kode_anggota.' GROUP BY kode_jenis_simpan';
            $simpananList = DB::select($query);
            foreach ($simpananList as $item)
            {

                // get next serial number
                $nextSerialNumber = PenarikanManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
                $tabungan = Tabungan::where('kode_anggota', $request->kode_anggota)
                                    ->where('kode_trans', $item->kode_jenis_simpan)
                                    ->first();
                $penarikan = new Penarikan();

                DB::transaction(function () use ($penarikan, $item, $request, $user, $nextSerialNumber, $tabungan)
                {
                    $penarikan->kode_anggota = $request->kode_anggota;
                    $penarikan->kode_tabungan = $tabungan->kode_tabungan;
                    $penarikan->id_tabungan = $tabungan->id;
                    $penarikan->besar_ambil = $item->besar_simpanan;
                    $penarikan->code_trans = $tabungan->kode_trans;
                    $penarikan->tgl_ambil = Carbon::now();
                    $penarikan->u_entry = $user->name;
                    $penarikan->created_by = $user->id;
                    $penarikan->status_pengambilan = STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI;
                    $penarikan->serial_number = $nextSerialNumber;
                    $penarikan->is_exit_anggota = 1;
                    $penarikan->save();
                });

                event(new PenarikanCreated($penarikan, $tabungan));
            }

            return redirect()->back()->withSuccess('Berhasil mengirim pengajuan.');
        }
        catch (\Throwable $th)
        {
            $error = $th->getMessage().' || file'.$th->getFile().' || line'. $th->getLine();
            return redirect()->back()->withError($error);
        }
    }

    public function exportExcel(Request $request)
    {
        try
        {
            $user = Auth::user();

            $listPenarikan = Penarikan::with('anggota', 'tabungan', 'statusPenarikan', 'createdBy', 'approvedBy', 'paidByCashier', 'jurnals', 'akunDebet')
                                        ->orderBy('tgl_ambil','desc');

            if($request->status_penarikan != "")
            {
                $listPenarikan->where('status_pengambilan', $request->status_penarikan);
            }

            if($request->tgl_ambil != "")
            {
                $tgl_ambil = Carbon::createFromFormat('d-m-Y', $request->tgl_ambil)->toDateString();

                $listPenarikan->where('tgl_ambil', $tgl_ambil);
            }

            if($request->anggota != "")
            {
                $listPenarikan->where('kode_anggota', $request->anggota);
            }

            if ($user->isAnggota())
            {
                $anggota = $user->anggota;

                $listPenarikan->where('kode_anggota', $anggota->kode_anggota);
            }
            $listPenarikan = $listPenarikan->get();

            $data['listPenarikan'] = $listPenarikan;
            $name = 'List Penarikan'.Carbon::now()->toDateTimeString().'.xlsx';

            return Excel::download(new ListPenarikanExport($data), $name);
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '.$th->getFile().' || '.$th->getLine();
            Log::info($message);
            return redirect()->back()->withErrors($message);
        }
    }
}
