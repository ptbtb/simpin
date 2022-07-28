<?php

namespace App\Http\Controllers;

use App\Events\Penarikan\PenarikanApproved;
use App\Events\Penarikan\PenarikanCreated;
use App\Events\Pinjaman\PengajuanApproved;
use App\Imports\PinjamanBaruImport;
use Illuminate\Http\Request;
use App\Events\Pinjaman\PengajuanCreated;
use App\Events\Pinjaman\PengajuanPaid;
use App\Events\Pinjaman\PengajuanUpdated;
use App\Exports\PinjamanExport;
use App\Exports\LaporanPinjamanExcelExport;
use App\Models\Anggota;
use App\Models\JenisPenghasilan;
use App\Models\Pengajuan;
use App\Models\Pinjaman;
use App\Models\JenisPinjaman;
use App\Models\Penghasilan;
use App\Models\StatusPengajuan;
use App\Models\Code;
use App\Models\View\ViewSaldo;
use App\Events\Pinjaman\PinjamanCreated;
use App\Exports\DetailPinjamanExport;
use App\Exports\PengajuanPinjamanExport;
use App\Exports\PinjamanSingleExport;
use App\Exports\SaldoAwalPinjamanExport;
use App\Imports\PinjamanImport;
use App\Managers\JurnalManager;
use App\Managers\PengajuanManager;
use App\Managers\PinjamanManager;
use App\Managers\AngsuranManager;
use App\Managers\PenarikanManager;
use App\Managers\SimpananManager;
use App\Managers\AngsuranPartialManager;
use App\Models\Angsuran;
use App\Models\Company;
use App\Models\Penarikan;
use App\Models\Tabungan;
use App\Models\SimpinRule;
use App\Models\Jurnal;
use App\Models\SumberDana;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\ModelNotfoundException;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->roles->first();
        $this->authorize('view pinjaman', $user);
        $listtenor = JenisPinjaman::pluck('lama_angsuran', 'lama_angsuran')->sortBy('lama_angsuran');
        // check role user
        if ($user->roles->first()->id == ROLE_ANGGOTA) {
            $anggota = $user->anggota;
            if (is_null($anggota)) {
                return redirect()->back()->withError('Your account has no members');
            }

            $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                ->where('id_status_pinjaman', STATUS_PINJAMAN_BELUM_LUNAS)
                ->wherenotnull('tgl_transaksi')
                ->orderBy('tgl_entri', 'asc');
        } else {
            if ($request->id) {
                $anggota = Anggota::find($request->id);

                $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                    // ->where('id_status_pinjaman', STATUS_PINJAMAN_BELUM_LUNAS)
                    ->wherenotnull('tgl_transaksi');
            } else {
                $listPinjaman = Pinjaman::wherenotnull('tgl_transaksi')
                    ->where('id_status_pinjaman', STATUS_PINJAMAN_BELUM_LUNAS)
                    ->orderby('created_at', 'desc');
            }
        }

        if (!$request->from) {
            if ($request->id) {
                $request->from = Carbon::createFromFormat('Y-m-d', '2021-01-01')->format('Y-m-d');
            } else {
                $request->from = Carbon::now()->startOfDay()->format('Y-m-d');
            }
        } else {
            $request->from = Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay()->format('Y-m-d');
        }

        if (!$request->to) {
            $request->to = Carbon::now()->endOfDay()->format('Y-m-d');
        } else {
            $request->to = Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay()->format('Y-m-d');
        }
        if ($request->jenistrans) {
            if ($request->jenistrans == 'A') {
                $listPinjaman = Pinjaman::where('saldo_mutasi', '>', 0);
            }
            if ($request->jenistrans == 'T') {
                $listPinjaman = Pinjaman::where('saldo_mutasi', 0);
            }
        }
        if ($request->unit_kerja) {
            $listPinjaman = $listPinjaman->whereHas('anggota', function ($query) use ($request) {
                return $query->where('company_id', $request->unit_kerja);
            });
        }
        if ($request->tenor) {
            $listPinjaman = $listPinjaman->where('lama_angsuran', $request->tenor);
        }
        $data['unitKerja'] = Company::get()->pluck('nama', 'id');
        $listPinjaman = $listPinjaman->whereBetween('tgl_entri', [$request->from, $request->to]);
        $listPinjaman = $listPinjaman->get();
        $data['title'] = "List Pinjaman";
        $data['listPinjaman'] = $listPinjaman;
        $data['request'] = $request;
        $data['role'] = $role;
        $data['listtenor'] = $listtenor;
        return view('pinjaman.index', $data);
    }

    public function indexSingle(Request $request, $id)
    {
        $user = Auth::user();
        $anggota = null;
        $role = $user->roles->first();
        $this->authorize('view pinjaman', $user);
        $listtenor = JenisPinjaman::pluck('lama_angsuran', 'lama_angsuran')->sortBy('lama_angsuran');
        // check role user
        if ($user->roles->first()->id == ROLE_ANGGOTA) {
            $anggota = $user->anggota;
            if (is_null($anggota)) {
                return redirect()->back()->withError('Your account has no members');
            }

            $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                ->where('id_status_pinjaman', STATUS_PINJAMAN_BELUM_LUNAS)
                ->wherenotnull('tgl_transaksi')
                ->orderBy('tgl_entri', 'asc');
        } else {

            $anggota = Anggota::find($id);
            $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                // ->where('id_status_pinjaman', STATUS_PINJAMAN_BELUM_LUNAS)
                ->wherenotnull('tgl_transaksi');
        }


        if ($request->jenistrans) {
            if ($request->jenistrans == 'A') {
                $listPinjaman = $listPinjaman->where('saldo_mutasi', '>', 0);
            }
            if ($request->jenistrans == 'T') {
                $listPinjaman = $listPinjaman->where('saldo_mutasi', 0);
            }
        }

        // $data['unitKerja'] = Company::get()->pluck('nama', 'id');
        // $listPinjaman = $listPinjaman->whereBetween('tgl_entri', [$request->from, $request->to]);
        $listPinjaman = $listPinjaman->get();
        // dd($listPinjaman->pluck('id'));
        $data['title'] = "List Pinjaman";
        $data['listPinjaman'] = $listPinjaman;
        $data['request'] = $request;
        $data['role'] = $role;
        $data['listtenor'] = $listtenor;
        $data['anggota'] = $anggota;
        return view('pinjaman.indexAnggota', $data);
    }

    public function indexPengajuan(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pengajuan pinjaman', $user);

        if ($user->isAnggota()) {
            $anggota = $user->anggota;
            if (is_null($anggota)) {
                return redirect()->back()->withError('Your account has no members');
            }

            // $listPengajuanPinjaman = Pengajuan::where('kode_anggota', $anggota->kode_anggota)->orderBy('tgl_pengajuan', 'asc')
            // ->get();
        } else {
            // $listPengajuanPinjaman = Pengajuan::with('anggota')->orderBy('tgl_pengajuan', 'asc')->get();
        }
        if (!$request->start_tgl_pengajuan) {
            $request->start_tgl_pengajuan = Carbon::today()->startOfDay()->format('d-m-Y');
        }
        if (!$request->end_tgl_pengajuan) {
            $request->end_tgl_pengajuan = Carbon::today()->endOfDay()->format('d-m-Y');
        }
        $bankAccounts = Code::where('CODE', 'like', '102%')->where('is_parent', 0)->get();

        $statusPengajuans = StatusPengajuan::get();
        $listSumberDana = SumberDana::with('codes')
            ->whereIn('id', [1, 2, 3])
            ->get();

        // $anggotas = Anggota::get();

        $data['title'] = "List Pengajuan Pinjaman";
        $data['listPengajuanPinjaman'] = collect();;
        $data['request'] = $request;
        $data['bankAccounts'] = $bankAccounts;
        $data['statusPengajuans'] = $statusPengajuans;
        $data['listSumberDana'] = $listSumberDana;
        // $data['anggotas'] = $anggotas;
        return view('pinjaman.indexPengajuan', $data);
    }


    /**
     * Display a listing of the resource through ajax.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPengajuanAjax(Request $request)
    {
        try {
            $user = Auth::user();
            $this->authorize('view pengajuan pinjaman', $user);

            $listPengajuanPinjaman = Pengajuan::with('anggota', 'createdBy', 'approvedBy', 'pinjaman', 'paidByCashier', 'jenisPinjaman', 'statusPengajuan', 'pengajuanTopup', 'akunDebet', 'jenisPenghasilan');

            if ($request->status_pengajuan != "") {
                $listPengajuanPinjaman = $listPengajuanPinjaman->where('id_status_pengajuan', $request->status_pengajuan);
            } else {
                $listPengajuanPinjaman = $listPengajuanPinjaman->whereNotIn('id_status_pengajuan', [8, 9, 10]);
            }
            if ($request->start_tgl_pengajuan) {
                $start = Carbon::createFromFormat('d-m-Y', $request->start_tgl_pengajuan)->startOfDay();
            }
            if ($request->end_tgl_pengajuan) {
                $start = Carbon::createFromFormat('d-m-Y', $request->end_tgl_pengajuan)->endOfDay();
            }
            // if ($request->start_tgl_pengajuan != "") {
            //     $start = Carbon::createFromFormat('d-m-Y', $request->start_tgl_pengajuan);
            //     // $listPengajuanPinjaman = $listPengajuanPinjaman->where('tgl_pengajuan', '>=', $tgl_pengajuan);
            // }
            // else
            // {
            //     $start = Carbon::now()->startOfDay();
            //     // $listPengajuanPinjaman = $listPengajuanPinjaman->where('tgl_pengajuan', '>=', $tgl_pengajuan);
            // }
            //
            // if ($request->end_tgl_pengajuan != "") {
            //     $end = Carbon::createFromFormat('d-m-Y', $request->end_tgl_pengajuan);
            //     // $listPengajuanPinjaman = $listPengajuanPinjaman->where('tgl_pengajuan', '<=', $tgl_pengajuan);
            // }
            // else
            // {
            //     $end = Carbon::now()->endOfDay();
            //     // $listPengajuanPinjaman = $listPengajuanPinjaman->where('tgl_pengajuan', '<=', $tgl_pengajuan);
            // }

            if ($request->anggota != "") {
                $listPengajuanPinjaman = $listPengajuanPinjaman->where('kode_anggota', $request->anggota);
            }

            if ($user->isAnggota()) {
                $anggota = $user->anggota;
                if (is_null($anggota)) {
                    return redirect()->back()->withError('Your account has no members');
                }

                $listPengajuanPinjaman = $listPengajuanPinjaman->where('kode_anggota', $anggota->kode_anggota)
                    ->wherebetween('tgl_pengajuan', [$request->start_tgl_pengajuan, $request->end_tgl_pengajuan]);
            }

            return Datatables::eloquent($listPengajuanPinjaman)
                ->editColumn('tgl_pengajuan', function ($request) {
                    if ($request->tgl_pengajuan) {
                        return $request->tgl_pengajuan->format('d M Y');
                    }
                })
                ->editColumn('nama_pinjaman', function ($request) {
                    return ucwords(strtolower($request->jenisPinjaman->nama_pinjaman));
                })
                ->editColumn('besar_pinjam', function ($request) {
                    return "Rp " . number_format($request->besar_pinjam, 0, ",", ".");
                })
                ->editColumn('status_pengajuan', function ($request) {
                    return ucfirst($request->statusPengajuan->name);
                })
                ->editColumn('tgl_acc', function ($request) {
                    if ($request->tgl_acc) {
                        return $request->tgl_acc->format('d M Y');
                    }
                })
                ->addIndexColumn()
                ->make(true);
        } catch (\Throwable $e) {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history pinjaman', $user);

        if ($user->roles->first()->id == ROLE_ANGGOTA) {
            $anggota = $user->anggota;
            if (is_null($anggota)) {
                return redirect()->back()->withError('Your account has no members');
            }

            $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                ->paid();
        } else {
            $listPinjaman = Pinjaman::paid();
        }

        if ($request->from) {
            $listPinjaman = $listPinjaman->where('tgl_entri', '>=', $request->from);
        }
        if ($request->to) {
            $listPinjaman = $listPinjaman->where('tgl_entri', '<=', $request->to);
        }
        $listPinjaman = $listPinjaman->get();
        $data['title'] = "History Pinjaman";
        $data['listPinjaman'] = $listPinjaman;
        $data['request'] = $request;
        return view('pinjaman.history', $data);
    }

    public function show($id)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        $pinjaman = Pinjaman::with('anggota', 'listAngsuran.jurnals')
            ->where('kode_pinjam', $id)
            ->first();

        $tabungan = Tabungan::where('kode_anggota', $pinjaman->kode_anggota)
            ->where('kode_trans', '!=', '411.01.000')
            ->get();

        $listAngsuran = $pinjaman->listAngsuran->sortBy('angsuran_ke')->values();
        // $tagihan = $listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS)->first();
        $bankAccounts = Code::where('CODE', 'like', '102%')->where('is_parent', 0)->get();

        $listSumberDana = SumberDana::with('codes')
            ->whereIn('id', [1, 2, 3])
            ->get();

        $data['pinjaman'] = $pinjaman;
        $data['title'] = 'Detail Pinjaman';
        $data['jenisPinjaman'] = $pinjaman->jenisPinjaman;
        $data['listAngsuranLunas'] = $listAngsuran->where('besar_pembayaran', '>', 0)->values();
        $data['listAngsuran'] = $listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_LUNAS)->values();
        // $data['tagihan'] = $tagihan;
        $data['bankAccounts'] = $bankAccounts;
        $data['tabungan'] = $tabungan;
        $data['listSumberDana'] = $listSumberDana;
        return view('pinjaman.detail', $data);
    }

    public function downloadFormPinjaman(Request $request)
    {
        $user = Auth::user();
        $this->authorize('download form pinjaman', $user);
        $data['title'] = 'Download Form Pinjaman';
        $data['listJenisPinjaman'] = JenisPinjaman::all();
        $data['sumberDana'] = JenisPenghasilan::orderBy('sequence', 'asc')->get();
        return view('pinjaman.downloadFormPinjaman', $data);
    }

    public function createExcel(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);
        $anggota = $user->anggota;
        $request->anggota = $anggota;
        $filename = 'export_pinjaman_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        return Excel::download(new PinjamanExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function createExcelSingle(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);
        $anggota = Anggota::find($request->kodeAnggota);
        $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
            ->where('id_status_pinjaman', STATUS_PINJAMAN_BELUM_LUNAS)
            ->wherenotnull('tgl_transaksi')
            ->get();
        $data['listPinjaman'] = $listPinjaman;
        $filename = 'export_pinjaman_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        return Excel::download(new PinjamanSingleExport($data), $filename);
    }

    public function createExcelDetail($id, Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        $pinjaman = Pinjaman::with('anggota', 'listAngsuran.jurnals')
            ->where('kode_pinjam', $id)
            ->first();

        $tabungan = Tabungan::where('kode_anggota', $pinjaman->kode_anggota)
            ->where('kode_trans', '!=', '411.01.000')
            ->get();

        $listAngsuran = $pinjaman->listAngsuran->sortBy('angsuran_ke')->values();
        $tagihan = $listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS)->first();
        $bankAccounts = Code::where('CODE', 'like', '102%')->where('is_parent', 0)->get();

        $data['pinjaman'] = $pinjaman;
        $data['title'] = 'Detail Pinjaman';
        $data['jenisPinjaman'] = $pinjaman->jenisPinjaman;
        $data['listAngsuran'] = $listAngsuran;
        $data['tagihan'] = $tagihan;
        $data['bankAccounts'] = $bankAccounts;
        $data['tabungan'] = $tabungan;
        // return view('pinjaman.detail', $data);

        $filename = 'export_pinjaman_excel_detail_' . $pinjaman->anggota->nama_anggota . '.xlsx';
        return Excel::download(new DetailPinjamanExport($data), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function createPDF(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        if ($user->roles->first()->id == ROLE_ANGGOTA) {
            $anggota = $user->anggota;
            if (is_null($anggota)) {
                return redirect()->back()->withError('Your account has no members');
            }

            $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota);
        } else {
            $listPinjaman = Pinjaman::with('anggota');
        }

        if (!$request->from) {
            $request->from = Carbon::today()->firstOfMonth()->format('Y-m-d');
        }
        if (!$request->to) {
            $request->to = Carbon::today()->format('Y-m-d');
        }
        if ($request->status) {
            $listPinjaman = $listPinjaman->where('id_status_pinjaman', $request->status);
        }
        if ($request->jenistrans) {
            if ($request->jenistrans == 'A') {
                $listPinjaman = Pinjaman::where('saldo_mutasi', '>', 0);
            }
            if ($request->jenistrans == 'T') {
                $listPinjaman = Pinjaman::where('saldo_mutasi', 0);
            }
        }
        if ($request->unit_kerja) {
            $r = $request;
            $listPinjaman = $listPinjaman->whereHas('anggota', function ($query) use ($r) {
                return $query->where('company_id', $r->unit_kerja);
            });
        }
        if ($request->tenor) {
            $listPinjaman = $listPinjaman->where('lama_angsuran', $request->tenor);
        }
        $listPinjaman = $listPinjaman->whereBetween('tgl_entri', [$request->from, $request->to]);
        $listPinjaman = $listPinjaman->get();

        // share data to view
        view()->share('listPinjaman', $listPinjaman);
        $pdf = PDF::loadView('pinjaman.excel', $listPinjaman)->setPaper('a4', 'landscape');

        // download PDF file with download method
        $filename = 'export_pinjaman_' . Carbon::now()->format('d M Y') . '.pdf';
        return $pdf->download($filename);
    }

    public function createPDFSingle(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        $anggota = Anggota::find($request->kodeAnggota);
        $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
            ->where('id_status_pinjaman', STATUS_PINJAMAN_BELUM_LUNAS)
            ->wherenotnull('tgl_transaksi')
            ->get();
        $data['listPinjaman'] = $listPinjaman;

        // share data to view
        view()->share('listPinjaman', $listPinjaman);
        $pdf = PDF::loadView('pinjaman.excel1', $listPinjaman)->setPaper('a4', 'landscape');

        // download PDF file with download method
        $filename = 'export_pinjaman_' . Carbon::now()->format('d M Y') . '.pdf';
        return $pdf->download($filename);
    }

    public function createPDF1(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);
        $anggota = $user->anggota;
        $request->anggota = $anggota;
        $filename = 'export_pinjaman_excel_' . Carbon::now()->format('d M Y') . '.pdf';
        return Excel::download(new PinjamanExport($request), $filename, \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function createPengajuanPinjaman()
    {
        $user = Auth::user();
        $this->authorize('add pengajuan pinjaman', $user);
        $listPinjaman = null;
        $data['sumberDana'] = JenisPenghasilan::orderBy('sequence', 'asc')->get();
        if ($user->isAnggota()) {
            $listPinjaman = Pinjaman::japan()
                ->where('kode_anggota', $user->anggota->kode_anggota)
                ->where('id_status_pinjaman', STATUS_PINJAMAN_BELUM_LUNAS)
                ->get();
        }

        $data['title'] = 'Buat Pengajuan Pinjaman';
        $data['listJenisPinjaman'] = JenisPinjaman::all();
        $data['listPinjaman'] = $listPinjaman;
        return view('pinjaman.createPengajuanPinjaman', $data);
    }

    public function storePengajuanPinjaman(Request $request)
    {
        $user = Auth::user();
        $this->authorize('add pengajuan pinjaman', $user);

        $besarPinjaman = filter_var($request->besar_pinjaman, FILTER_SANITIZE_NUMBER_INT);
        $requestSimpananPagu = filter_var($request->simpanan_pagu, FILTER_SANITIZE_NUMBER_INT);
        $maksimalPinjaman = filter_var($request->maksimal_besar_pinjaman, FILTER_SANITIZE_NUMBER_INT);

        //  chek pengajuan yang belum accepted
        $jenisPinjaman = JenisPinjaman::find($request->jenis_pinjaman);
        $checkPengajuan = Pengajuan::whereraw("SUBSTRING(kode_jenis_pinjam,1,6)=" . substr($jenisPinjaman->kode_jenis_pinjam, 0, 6) . " ")
            ->notApproved()
            ->where('kode_anggota', $request->kode_anggota)
            ->get();
        if ($request->term == 'false') {
            if ($checkPengajuan->count()) {
                return redirect()->back()->withError('Pengajuan pinjaman gagal. Anda sudah pernah mengajukan pinjaman untuk jenis pinjaman ' . $jenisPinjaman->nama_pinjaman);
            }
        }



        // check if topup
        $listTopupPinjaman = collect([]);
        if ($request->jenis_pengajuan == JENIS_PENGAJUAN_TOPUP) {
            // kalkulasi semua sisa pinjamannya
            $listTopupPinjaman = Pinjaman::whereIn('kode_pinjam', $request->topup_pinjaman)->get();
            $totalPinjaman = $listTopupPinjaman->sum('totalBayarTopup');
            if ($besarPinjaman < $totalPinjaman) {
                return redirect()->back()->withError('Besar pinjaman lebih kecil dari total sisa pinjaman yang di topup');
            }
        } else {
            // check pinjaman yang belum lunas
            $checkPinjaman = Pinjaman::whereraw("SUBSTRING(kode_jenis_pinjam,1,6)=" . substr($jenisPinjaman->kode_jenis_pinjam, 0, 6) . " ")
                ->notPaid()
                ->where('kode_anggota', $request->kode_anggota)
                ->get();
            if ($request->term == 'false') {
                if ($checkPinjaman->count()) {
                    return redirect()->back()->withError('Pengajuan pinjaman gagal. Anda masih memiliki pinjaman dengan jenis pinjaman ' . $jenisPinjaman->nama_pinjaman . ' yang belum lunas');
                }
            }
        }

        //check gaji
        $anggota = Anggota::find($request->kode_anggota);
        $jenisPenghasilan = JenisPenghasilan::where('company_group_id', $anggota->company->company_group_id)
            ->where('rule_name', 'gaji_bulanan')
            ->first();
        $gajiraw = Penghasilan::where('kode_anggota', $request->kode_anggota)
            ->where('id_jenis_penghasilan', $request->sumber_dana)
            ->first();
        if (!$gajiraw){
            return redirect()->back()->withError('Belum memiliki data penghasilan ');
        }
        if ($gajiraw->value == 0) {
            return redirect()->back()->withError('Belum memiliki penghasilan ' . $gajiraw->jenisPenghasilan->name);
        }
        $gaji = $gajiraw->value;
        $potonganGaji = 0.65 * $gaji;
        $angsuranPerbulan = $besarPinjaman / $request->lama_angsuran;
        if ($request->term == 'false') {
            if ($angsuranPerbulan > $potonganGaji) {
                return redirect()->back()->withError('Pengajuan pinjaman gagal. Jumlah pinjaman yang anda ajukan melebihi batas 65 % ' . $gajiraw->jenisPenghasilan->name . ' Anda.');
            }
        }


        $isCreatePagu = 0;
        $transferPagu = 0;
        $saldoSimpanan = 0;

        $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
        if ($saldo) {
            $saldojumlah = $saldo->jumlah;
        } else {
            $saldojumlah = 0;
        }

        if ($jenisPinjaman->isDanaKopegmar()) {
            $saldoSimpanan = $saldojumlah * 5;
            $pengali = 5;
        } elseif ($jenisPinjaman->isDanaLain()) {
            $saldoSimpanan =  $saldojumlah * 8;
            $pengali = 8;
        }

        // if ($saldoSimpanan < $besarPinjaman) {
        if ($requestSimpananPagu > 0) {
            $isCreatePagu = 1;
            // $transferPagu = ($besarPinjaman / $pengali) - $saldojumlah;
            $transferPagu = $requestSimpananPagu;
        }


        $pengajuan = null;
        DB::transaction(function () use ($request, $besarPinjaman, $user, &$pengajuan, $isCreatePagu, $transferPagu) {
            $kodeAnggota = $request->kode_anggota;
            $kodePengajuan = str_replace('.', '', $request->jenis_pinjaman) . '-' . $kodeAnggota . '-' . Carbon::now()->format('dmYHis');

            $pengajuan = new Pengajuan();
            $pengajuan->kode_pengajuan = $kodePengajuan;
            // $pengajuan->tgl_pengajuan = Carbon::now();
            $pengajuan->tgl_pengajuan = Carbon::createFromFormat('Y-m-d', $request->tanggal_pengajuan);
            $pengajuan->kode_anggota = $request->kode_anggota;
            $pengajuan->kode_jenis_pinjam = $request->jenis_pinjaman;
            $pengajuan->besar_pinjam = $besarPinjaman;
            $pengajuan->keperluan = $request->keperluan;
            if ($user->roles->first()->id == ROLE_ADMIN) {
                $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_KETUA;
            }else{
               $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_KONFIRMASI; 
            }
            
            $pengajuan->sumber_dana = $request->sumber_dana;
            $pengajuan->tenor = $request->lama_angsuran;
            $pengajuan->biaya_administrasi = filter_var($request->biaya_administrasi, FILTER_SANITIZE_NUMBER_INT);
            $pengajuan->biaya_jasa = filter_var($request->biaya_jasa, FILTER_SANITIZE_NUMBER_INT);
            $pengajuan->biaya_provisi = filter_var($request->biaya_provisi, FILTER_SANITIZE_NUMBER_INT);
            $pengajuan->biaya_asuransi = filter_var($request->biaya_asuransi, FILTER_SANITIZE_NUMBER_INT);
            $pengajuan->biaya_jasa_topup = filter_var($request->biaya_jasa_topup, FILTER_SANITIZE_NUMBER_INT);
            $pengajuan->created_by = $user->id;

            if ($isCreatePagu) {
                $pengajuan->transfer_simpanan_pagu = $transferPagu;
            }

            $file = $request->form_persetujuan;
            if ($file) {
                $config['disk'] = 'upload';
                $config['upload_path'] = '/pengajuanpinjaman/' . $user->id . '/form';
                $config['public_path'] = env('APP_URL') . '/upload/pengajuanpinjaman/' . $user->id . '/form';

                // create directory if doesn't exist
                if (!Storage::disk($config['disk'])->has($config['upload_path'])) {
                    Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
                }

                // upload file if valid
                if ($file->isValid()) {
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                    Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file, $filename);
                    $pengajuan->form_persetujuan = $config['disk'] . $config['upload_path'] . '/' . $filename;
                }
            }

            $pengajuan->save();
        });

        if ($pengajuan) {
            if ($request->jenis_pengajuan == JENIS_PENGAJUAN_TOPUP) {
                PengajuanManager::createPengajuanTopup($pengajuan, $listTopupPinjaman);
            }
            event(new PengajuanCreated($pengajuan));
        }

        return redirect()->route('pengajuan-pinjaman-list')->withSuccess('Pengajuan pinjaman telah dibuat dan menunggu persetujuan.');
    }

    public function updateStatusPengajuanPinjaman(Request $request)
    {
        try {
            $user = Auth::user();
            $check = Hash::check($request->password, $user->password);
            if (!$check) {
                Log::error('Wrong Password');
                return response()->json(['message' => 'Wrong Password'], 412);
            }

            // get kode ambil's data when got from check boxes
            if (isset($request->ids)) {
                $ids = json_decode($request->ids);
            }
            \Log::info($request);
            foreach ($ids as $key => $id) {
                $pengajuan = Pengajuan::where('id', $id)->first();

                // check pengajuan's status must same as old_status
                if ($pengajuan && $pengajuan->id_status_pengajuan == $request->old_status) {
                    if ($request->status == STATUS_PENGAJUAN_PINJAMAN_DIBATALKAN) {
                        if ($pengajuan->pinjaman) {
                            $pengajuan->pinjaman->each(function ($pinjam) {
                                $pinjam->listAngsuran->each(function ($angsur) {
                                    $angsur->delete();
                                });
                                $pinjam->delete();
                            });
                        }
                        $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_DIBATALKAN;
                        $pengajuan->save();
                        return response()->json(['message' => 'success'], 200);
                    }


                    if (is_null($pengajuan)) {
                        return response()->json(['message' => 'not found'], 404);
                    }

                    if ($request->status == STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_BENDAHARA) {
                        $this->authorize('approve pengajuan pinjaman', $user);
                        $statusPengajuanSekarang = $pengajuan->statusPengajuan;
                        if ($pengajuan->besar_pinjam <= $statusPengajuanSekarang->batas_pengajuan) {
                            $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN;
                        } else {
                            $pengajuan->id_status_pengajuan = $request->status;
                        }
                    } elseif ($request->status == STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_KETUA) {
                        $this->authorize('approve pengajuan pinjaman', $user);
                        $statusPengajuanSekarang = $pengajuan->statusPengajuan;
                        if ($pengajuan->besar_pinjam <= $statusPengajuanSekarang->batas_pengajuan) {
                            $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN;
                        } else {
                            $pengajuan->id_status_pengajuan = $request->status;
                        }
                    } else {
                        $pengajuan->id_status_pengajuan = $request->status;
                    }

                    $pengajuan->tgl_acc = Carbon::now();
                    $pengajuan->approved_by = $user->id;

                    if ($request->status == STATUS_PENGAJUAN_PINJAMAN_DITERIMA) {
                        $this->authorize('bayar pengajuan pinjaman', $user);
                        $pengajuan->paid_by_cashier = $user->id;
                        $file = $request->bukti_pembayaran;

                        if ($file) {
                            $config['disk'] = 'upload';
                            $config['upload_path'] = '/pinjaman/pengajuan/' . $pengajuan->kode_pengajuan . '/bukti-pembayaran/';
                            $config['public_path'] = env('APP_URL') . '/upload/pinjaman/pengajuan/' . $pengajuan->kode_pengajuan . '/bukti-pembayaran/';

                            // create directory if doesn't exist
                            if (!Storage::disk($config['disk'])->has($config['upload_path'])) {
                                Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
                            }

                            // upload file if valid
                            if ($file->isValid()) {
                                $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                                Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file, $filename);
                                $pengajuan->bukti_pembayaran = $config['disk'] . $config['upload_path'] . '/' . $filename;
                            }
                        }

                        if ($request->id_akun_debet) {
                            $pengajuan->id_akun_debet = $request->id_akun_debet;

                            $pinjaman = $pengajuan->pinjaman;

                            if ($pinjaman) {
                                $pinjaman->id_akun_kredit = $request->id_akun_debet;
                                // dd($request->tgl_transaksi);
                                $pinjaman->tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi)->format('Y-m-d');
                                $pinjaman->tgl_tempo = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi)->addMonths($pinjaman->lama_angsuran + 1)->format('Y-m-d');
                                $pinjaman->serial_number_kredit = PinjamanManager::getSerialNumberKredit(Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi));
                                $pinjaman->save();
                            }
                        }

                        $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_DITERIMA;
                        $pengajuan->tgl_transaksi = $request->tgl_transaksi;
                    }

                    if ($request->keterangan) {
                        $pengajuan->keterangan = $request->keterangan;
                    }

                    $pengajuan->save();
                    if ($pengajuan->menungguPembayaran() && is_null($pengajuan->pinjaman)) {
                        event(new PengajuanApproved($pengajuan));
                    }

                    if ($pengajuan->diterima() && $pengajuan->pinjaman) {
                        JurnalManager::createJurnalPinjaman($pengajuan->pinjaman);
                        AngsuranManager::syncAngsuran($pengajuan->pinjaman);
                        if ($pengajuan->pengajuanTopup->count()) {
                            $pengajuan->pengajuanTopup->each(function ($topup) {
                                $pinjamandata = $topup->pinjaman;
                                PinjamanManager::pembayaranPinjamanDipercepat($pinjamandata);
                            });
                        }

                        if ($pengajuan->transfer_simpanan_pagu) {
                            SimpananManager::createSimpananPagu($pengajuan);
                        }

                        event(new PengajuanPaid($pengajuan));
                    }

                    if ($pengajuan->ditolak()) {
                        $pengajuan->pengajuanTopup->each(function ($topup) {
                            $topup->delete();
                        });
                    }

                    event(new PengajuanUpdated($pengajuan));
                } else {
                    if ($request->status == STATUS_PENGAJUAN_PINJAMAN_DIBATALKAN) {
                        if ($pengajuan->pinjaman) {
                            $pengajuan->pinjaman->listAngsuran->each(function ($angsur) {
                                $angsur->delete();
                            });
                            $pengajuan->pinjaman->delete();
                        }
                        $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_DIBATALKAN;
                        $pengajuan->save();
                        return response()->json(['message' => 'success'], 200);
                    }
                }
            }

            return response()->json(['message' => 'success'], 200);
        } catch (\Exception $e) {
            \Log::error($e);
            $message = $e->getMessage();
            // return $e;
            return response()->json(['message' => $message], 500);
        }
    }

    public function calculateMaxPinjaman(Request $request)
    {

        if ($request->role == 'false') {
            $jenisPinjaman = JenisPinjaman::find($request->kode_jenis_pinjaman);
            // dd($jenisPinjaman);
            if (is_null($jenisPinjaman)) {
                return 0;
            }
            $anggota = Anggota::find($request->kode_anggota);
            // dd($anggota);
            if (is_null($anggota)) {
                return 0;
            }
            $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
            // dd($jenisPinjaman->isJangkaPanjang());
            // dd($anggota->isAnggotaBiasa());
            if (is_null($saldo)) {
                return 0;
            }


            if ($jenisPinjaman->isJangkaPanjang()) {
                if ($anggota->isPensiunan()) {
                    $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                    return $saldo->jumlah * 0.75;
                } elseif ($anggota->isAnggotaBiasa()) {
                    $jenisPenghasilan = JenisPenghasilan::where('company_group_id', $anggota->company->company_group_id)
                        ->where('rule_name', 'gaji_bulanan')
                        ->first();
                    // dd($jenisPenghasilan->id);
                    $gaji = Penghasilan::where('kode_anggota', $request->kode_anggota)
                        ->where('id_jenis_penghasilan', $jenisPenghasilan->id)
                        ->first();
                    // dd($gaji);

                    if (is_null($gaji)) {
                        return 0;
                    }
                    $gaji = $gaji->value;
                    $potonganGaji = 0.65 * $gaji;
                    return $potonganGaji * $jenisPinjaman->lama_angsuran;

                    /*if ($jenisPinjaman->isDanaKopegmar()) {
                    $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                    return $saldo->jumlah * 5;
                } elseif ($jenisPinjaman->isDanaLain()) {
                    $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                    return $saldo->jumlah * 8;
                }*/
                } elseif ($anggota->isAnggotaLuarBiasa()) {
                    $company = $anggota->company;
                    if ($company->isKopegmarGroup()) {
                        return 30000000;
                    }
                    if ($company->isKojaGroup()) {
                        $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                        return $saldo->jumlah * 5;
                    }
                }
            } elseif ($jenisPinjaman->isJangkaPendek()) {
                $penghasilanTertentu = Penghasilan::where('kode_anggota', $anggota->kode_anggota)
                    ->penghasilanTertentu()
                    ->get();
                if (!$penghasilanTertentu->count()) {
                    return response()->json(['message' => 'Tidak memiliki penghasilan tertentu'], 412);
                }

                $jumlahPenghasilanTertentu = $penghasilanTertentu->sum('value');
                if ($anggota->isAnggotaBiasa()) {
                    return 999999999;
                } elseif ($anggota->isAnggotaLuarBiasa()) {
                    return 100000000;
                } elseif ($anggota->isPensiunan()) {
                    $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                    return $saldo->jumlah * 0.75;
                }
            }
            return 0;
        } else {
            $jenisPinjaman = JenisPinjaman::find($request->kode_jenis_pinjaman);
            if (is_null($jenisPinjaman)) {
                return 0;
            }
            $anggota = Anggota::find($request->kode_anggota);
            if (is_null($anggota)) {
                return 0;
            }


            return 999999999999;
        }
    }

    public function simulasiPinjaman(Request $request)
    {
        //dd($request);
        $anggota = Anggota::find($request->kode_anggota);
        $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
        $jenisPinjaman = JenisPinjaman::find($request->jenis_pinjaman);
        $besarPinjaman = filter_var($request->besar_pinjaman, FILTER_SANITIZE_NUMBER_INT);
        $maksimalBesarPinjaman = filter_var($request->maksimal_besar_pinjaman, FILTER_SANITIZE_NUMBER_INT);
        $lamaAngsuran = $request->lama_angsuran;
        $keperluan = $request->keperluan;

        // biaya administrasi
        $biayaAdministrasi = 0;
        $simpinRule = SimpinRule::find(SIMPIN_RULE_ADMINISTRASI);
        if ($besarPinjaman > $simpinRule->value) {
            $biayaAdministrasi = $simpinRule->amount;
        }

        //check gaji
        $anggota = Anggota::find($request->kode_anggota);
        $jenisPenghasilan = JenisPenghasilan::where('company_group_id', $anggota->company->company_group_id)
            ->where('rule_name', 'gaji_bulanan')
            ->first();
        $gaji = Penghasilan::where('kode_anggota', $request->kode_anggota)
            ->where('id_jenis_penghasilan', $jenisPenghasilan->id)
            ->first();

        if (is_null($gaji)) {
            return redirect()->back()->withError($anggota->nama_anggota . ' tidak memiliki gaji bulanan');
        }
        $gaji = $gaji->value;
        $potonganGaji = 0.65 * $gaji;

        $provisi = $jenisPinjaman->provisi;
        $provisi = round($besarPinjaman * $provisi, 2);

        $angsuranPokok = round($besarPinjaman / $lamaAngsuran, 2);

        $asuransi = $jenisPinjaman->asuransi;
        $asuransi = round($besarPinjaman * $asuransi, 2);

        $jasa = $jenisPinjaman->jasa;
        if ($besarPinjaman > 100000000 && $jenisPinjaman->lama_angsuran > 3 && $jenisPinjaman->isJangkaPendek()) {
            $jasa = 0.03;
        }
        $jasa = $besarPinjaman * $jasa;
        $jasa = round($jasa, 2);
        $angsuranPerbulan = $angsuranPokok + $jasa;
        $collection = [
            'anggota' => $anggota,
            'saldo' => $saldo,
            'jenisPinjaman' => $jenisPinjaman,
            'besarPinjaman' => $besarPinjaman,
            'maksimalBesarPinjaman' => $maksimalBesarPinjaman,
            'lamaAngsuran' => $lamaAngsuran,
            'biayaAdministrasi' => $biayaAdministrasi,
            'provisi' => $provisi,
            'asuransi' => $asuransi,
            'jasa' => $jasa,
            'angsuranPerbulan' => $angsuranPerbulan,
            'angsuranPokok' => $angsuranPokok,
            'keperluan' => $keperluan,
            'potonganGaji' => $potonganGaji,
            'sumberDana' => jenisPenghasilan::find($request->sumber_dana),
        ];

        $data = $collection;
        $data['collection'] = $collection;
        $data['title'] = 'Download Form Pinjaman';;
        return view('pinjaman.hasilSimulasi', $data);
    }

    public function generateFormPinjaman(Request $request)
    {
        $anggota = Anggota::find($request->anggota);
        $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
        $jenisPinjaman = JenisPinjaman::find($request->jenisPinjaman);
        $besarPinjaman = filter_var($request->besarPinjaman, FILTER_SANITIZE_NUMBER_INT);
        $maksimalBesarPinjaman = filter_var($request->maksimalBesarPinjaman, FILTER_SANITIZE_NUMBER_INT);
        $lamaAngsuran = $request->lamaAngsuran;

        // biaya administrasi
        $biayaAdministrasi = 0;
        $simpinRule = SimpinRule::find(SIMPIN_RULE_ADMINISTRASI);
        if ($besarPinjaman > $simpinRule->value) {
            $biayaAdministrasi = $simpinRule->amount;
        }
        $keperluan = $request->keperluan;

        $provisi = $jenisPinjaman->provisi;
        $provisi = round($besarPinjaman * $provisi, 2);

        $asuransi = $jenisPinjaman->asuransi;
        $asuransi = round($besarPinjaman * $asuransi, 2);

        $angsuranPokok = round($besarPinjaman / $lamaAngsuran, 2);

        $jasa = $jenisPinjaman->jasa;
        if ($besarPinjaman > 100000000 && $jenisPinjaman->lama_angsuran > 3 && $jenisPinjaman->isJangkaPendek()) {
            $jasa = $besarPinjaman * 3 / 100;
        }
        $jasa = $besarPinjaman * $jasa;
        $jasa = round($jasa, 2);
        $angsuranPerbulan = $angsuranPokok + $jasa;
        $terbilang = self::terbilang($besarPinjaman) . ' rupiah';


        $sisaPinjaman = json_decode("{}");
        $japan = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
            ->where('kode_jenis_pinjam', 'like', Str::of(JENIS_PINJAM_JAPAN)->append('%'))
            ->sum('sisa_pinjaman');
        $japen = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
            ->where('kode_jenis_pinjam', 'like', Str::of(JENIS_PINJAM_JAPEN)->append('%'))
            ->sum('sisa_pinjaman');
        $kredit_barang = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
            ->where('kode_jenis_pinjam', JENIS_PINJAM_KREDIT_BARANG)
            ->sum('sisa_pinjaman');
        $kredit_motor = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
            ->where('kode_jenis_pinjam', JENIS_PINJAM_KREDIT_MOTOR)
            ->sum('sisa_pinjaman');

        $sisaPinjaman->japan = $japan;
        $sisaPinjaman->japen = $japen;
        $sisaPinjaman->kredit_barang = $kredit_motor;
        $sisaPinjaman->kredit_motor = $kredit_motor;

        $data = [
            'anggota' => $anggota,
            'saldo' => $saldo,
            'jenisPinjaman' => $jenisPinjaman,
            'besarPinjaman' => $besarPinjaman,
            'besarPinjamanTerbilang' => $terbilang,
            'maksimalBesarPinjaman' => $maksimalBesarPinjaman,
            'lamaAngsuran' => $lamaAngsuran,
            'lamaAngsuranTerbilang' => self::terbilang($lamaAngsuran),
            'biayaAdministrasi' => $biayaAdministrasi,
            'provisi' => $provisi,
            'asuransi' => $asuransi,
            'jasa' => $jasa,
            'keperluan' => $keperluan,
            'angsuranPerbulan' => $angsuranPerbulan,
            'angsuranPokok' => $angsuranPokok,
            'sisaPinjaman' => $sisaPinjaman,
        ];
        view()->share('data', $data);
        PDF::setOptions(['margin-left' => 0, 'margin-right' => 0]);
        $pdf = PDF::loadView('pinjaman.formPersetujuan', $data)->setPaper('a4', 'portrait');

        // download PDF file with download method
        $filename = 'form_persetujuan_atasan' . Carbon::now()->format('d M Y') . '.pdf';
        return $pdf->download($filename);

        return view('pinjaman.formPersetujuan', $data);
    }

    public static function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } elseif ($nilai < 20) {
            $temp = self::penyebut($nilai - 10) . " belas";
        } elseif ($nilai < 100) {
            $temp = self::penyebut($nilai / 10) . " puluh" . self::penyebut($nilai % 10);
        } elseif ($nilai < 200) {
            $temp = " seratus" . self::penyebut($nilai - 100);
        } elseif ($nilai < 1000) {
            $temp = self::penyebut($nilai / 100) . " ratus" . self::penyebut($nilai % 100);
        } elseif ($nilai < 2000) {
            $temp = " seribu" . self::penyebut($nilai - 1000);
        } elseif ($nilai < 1000000) {
            $temp = self::penyebut($nilai / 1000) . " ribu" . self::penyebut($nilai % 1000);
        } elseif ($nilai < 1000000000) {
            $temp = self::penyebut($nilai / 1000000) . " juta" . self::penyebut($nilai % 1000000);
        } elseif ($nilai < 1000000000000) {
            $temp = self::penyebut($nilai / 1000000000) . " milyar" . self::penyebut(fmod($nilai, 1000000000));
        } elseif ($nilai < 1000000000000000) {
            $temp = self::penyebut($nilai / 1000000000000) . " trilyun" . self::penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    public static function terbilang($nilai)
    {
        if ($nilai < 0) {
            $hasil = "minus " . trim(self::penyebut($nilai));
        } else {
            $hasil = trim(self::penyebut($nilai));
        }
        return $hasil;
    }

    public function detailPembayaran($id)
    {
        $pengajuan = Pengajuan::find($id);
        if (is_null($pengajuan)) {
            return response()->json(['message' => 'Pengajuan Not Found'], 404);
        }

        $pinjaman = $pengajuan->pinjaman;
        if (is_null($pinjaman)) {
            return response()->json(['message' => 'Pinjaman Not Found'], 404);
        }
        $data['pinjaman'] = $pinjaman;
        $data['jenisPinjaman'] = $pinjaman->jenisPinjaman;
        return view('pinjaman.detailPembayaran', $data);
    }

    public function bayarAngsuran(Request $request, $id)
    {
        // dd($request);
        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::where('kode_pinjam', $id)->first();

            for ($i = 0; $i < count($request->besar_pembayaran); $i++) {
                $pembayaran = filter_var($request->besar_pembayaran[$i], FILTER_SANITIZE_NUMBER_INT);
                // dd($request->jenis_pembayaran[$i]);
                if ($request->jenis_pembayaran[$i])
                {
                    $kode = $request->jenis_pembayaran[$i];
                    $tabungan = Tabungan::where('kode_trans', $kode)
                                        ->where('kode_anggota', $pinjaman->kode_anggota)
                                        ->first();
                    $anggota = $pinjaman->anggota;

                    if ($tabungan->besar_tabungan < $pembayaran) {
                        return redirect()->back()->withError('Sisa tabungan tidak mencukupi untuk melakukan pembayaran');
                    }
                }

                /**
                 * cari angsuran lama, apakah sudah bayar, tapi belum lunas atau tidak.
                 * jika ada, maka pembayaran sekarang, ditambah dengan pembayaran sebelumnya
                 */
                $serialNumber = AngsuranManager::getSerialNumber(Carbon::now()->format('d-m-Y'));

                $angsuran = Angsuran::where('kode_pinjam', $pinjaman->kode_pinjam)
                                        ->where('angsuran_ke', strval($pinjaman->angsuran_sekarang))
                                        ->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS)
                                        ->first();

                if(is_null($angsuran))
                {
                    $angsuran = AngsuranManager::createAngsuran($pinjaman, $request);
                }

                if ($request->jenis_pembayaran[$i]) {
                    $codeCoa = Code::where('CODE', $tabungan->kode_trans)->first();
                    $angsuran->id_akun_kredit = $codeCoa->id;
                } else {
                    $angsuran->id_akun_kredit = ($request->id_akun_kredit[$i]) ? $request->id_akun_kredit[$i] : null;
                }

                if ($angsuran->besar_pembayaran) {
                    $pembayaran = $pembayaran + $angsuran->besar_pembayaran;
                }
                if($request->besar_pembayaran_jasa[$i])
                {
                    $pembayaranJasa =  filter_var($request->besar_pembayaran_jasa[$i], FILTER_SANITIZE_NUMBER_INT);
                    if($angsuran->besar_pembayaran_jasa < $angsuran->jasa)
                    {
                        $angsuran->besar_pembayaran_jasa = $pembayaranJasa + $angsuran->besar_pembayaran_jasa;
                    }
                    else
                    {
                        return redirect()->back()->withError('Jasa sudah dibayar lunas. silahkan kosongkan input field jasa');
                    }
                    $pembayaran = $pembayaran + $pembayaranJasa;
                }

                if ($pembayaran >= $angsuran->totalAngsuran) {
                    $angsuran->besar_pembayaran = $angsuran->totalAngsuran;
                    $angsuran->id_status_angsuran = STATUS_ANGSURAN_LUNAS;
                    $pinjaman->sisa_angsuran = $pinjaman->sisa_angsuran - 1;
                    $pinjaman->save();
                } else {
                    $angsuran->besar_pembayaran = $pembayaran;
                }
                $angsuran->paid_at = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
                $angsuran->u_entry = Auth::user()->name;
                $angsuran->save();

                // create JKM angsuran
                JurnalManager::createJurnalAngsuranBaru($angsuran, $request->besar_pembayaran_jasa[$i]);

                $angsuran->tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
                $angsuran->serial_number = $serialNumber;
                $angsuran->save();
                $pembayaran = $pembayaran - $angsuran->totalAngsuran;

                if ($pembayaran <= 0) {
                    $pinjaman->sisa_pinjaman = $pinjaman->sisa_pinjaman - $angsuran->besar_pembayaran;
                    $pinjaman->save();
                    break;
                }
                if ($pinjaman->sisa_pinjaman <= 0) {
                    $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
                    $pinjaman->save();
                }
            }
            DB::commit();
            return redirect()->back()->withSuccess('berhasil melakukan pembayaran');
        } catch (\Throwable $e) {
            // dd($e);
            \Log::error($e);
            $message = $e->getMessage();
            DB::rollback();
            return redirect()->back()->withError($e->getMessage());
        }
    }
    public function bayarAngsuranOld(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::where('kode_pinjam', $id)->first();
            $listAngsuran = $pinjaman->listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS)->sortBy('angsuran_ke')->values();

            for ($i = 0; $i < count($request->besar_pembayaran); $i++) {
                $pembayaran = filter_var($request->besar_pembayaran[$i], FILTER_SANITIZE_NUMBER_INT);
                $payment = filter_var($request->besar_pembayaran[$i], FILTER_SANITIZE_NUMBER_INT);
                if ($request->jenis_pembayaran[$i]) {
                    $kode = $request->jenis_pembayaran[$i];
                    $tabungan = Tabungan::where('kode_trans', $kode)
                        ->where('kode_anggota', $pinjaman->kode_anggota)
                        ->first();
                    $anggota = $pinjaman->anggota;

                    if ($tabungan->besar_tabungan < $pembayaran) {
                        return redirect()->back()->withError('Sisa tabungan tidak mencukupi untuk melakukan pembayaran');
                    }
                }

                foreach ($listAngsuran as $angsuran) {
                    $serialNumber = AngsuranManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
                    if ($angsuran->besar_pembayaran) {
                        $pembayaran = $pembayaran + $angsuran->besar_pembayaran;
                    }
                    if ($pembayaran >= $angsuran->totalAngsuran) {
                        $angsuran->besar_pembayaran = $angsuran->totalAngsuran;
                        $angsuran->id_status_angsuran = STATUS_ANGSURAN_LUNAS;
                        $pinjaman->sisa_angsuran = $pinjaman->sisa_angsuran - 1;
                        $pinjaman->save();
                    } else {
                        $angsuran->besar_pembayaran = $pembayaran;
                    }



                    $angsuran->paid_at = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
                    $angsuran->u_entry = Auth::user()->name;
                    if ($request->jenis_pembayaran[$i]) {
                        $codeCoa = Code::where('CODE', $tabungan->kode_trans)->first();
                        $angsuran->id_akun_kredit = $codeCoa->id;
                    } else {
                        $angsuran->id_akun_kredit = ($request->id_akun_kredit[$i]) ? $request->id_akun_kredit[$i] : null;
                    }

                    $angsuran->tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
                    $angsuran->serial_number = $serialNumber;
                    $angsuran->save();
                    AngsuranPartialManager::generate($angsuran, $pembayaran);
                    $pembayaran = $pembayaran - $angsuran->totalAngsuran;

                    // create JKM angsuran
                    // JurnalManager::createJurnalAngsuran($angsuran);

                    if ($pembayaran <= 0) {
                        $pinjaman->sisa_pinjaman = $angsuran->sisaPinjaman;
                        $pinjaman->save();
                        break;
                    }
                    if ($pinjaman->sisa_pinjaman <= 0) {
                        $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
                        $pinjaman->save();
                    }
                }

                // save tgl transaksi
                $pinjaman->tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
                $pinjaman->save();

                if ($request->jenis_pembayaran[$i]) {
                    $penarikan = new Penarikan();
                    // get next serial number
                    $nextSerialNumber = PenarikanManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
                    $kode = $request->jenis_pembayaran[$i];
                    $tabungan = Tabungan::where('kode_trans', $kode)->first();
                    $besarPenarikan = $request->besar_pembayaran[$i];
                    $anggota = $pinjaman->anggota;
                    $user = Auth::user();

                    DB::transaction(function () use ($besarPenarikan, $anggota, $tabungan, &$penarikan, $user, $nextSerialNumber, $pinjaman, $request) {
                        $penarikan->kode_anggota = $anggota->kode_anggota;
                        $penarikan->kode_tabungan = $tabungan->kode_tabungan;
                        $penarikan->id_tabungan = $tabungan->id;
                        $penarikan->besar_ambil = $besarPenarikan;
                        $penarikan->code_trans = $tabungan->kode_trans;
                        $penarikan->tgl_ambil = Carbon::now();
                        $penarikan->u_entry = $user->name;
                        $penarikan->created_by = $user->id;
                        $penarikan->status_pengambilan = STATUS_PENGAMBILAN_DITERIMA;
                        $penarikan->serial_number = $nextSerialNumber;
                        $penarikan->tgl_acc = Carbon::now();
                        $penarikan->tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
                        $penarikan->approved_by = $user->id;
                        $penarikan->is_pelunasan_dipercepat = 1;
                        $penarikan->paid_by_cashier = $user->id;
                        $penarikan->description = 'Pengambilan pelunasan angsuran untuk pinjaman ' . $pinjaman->kode_pinjam;
                        $penarikan->save();
                    });

                    // JurnalManager::createJurnalPenarikan($penarikan);
                }
            }
            DB::commit();
            return redirect()->back()->withSuccess('berhasil melakukan pembayaran');
        } catch (\Throwable $e) {
            \Log::error($e);
            $message = $e->getMessage();
            DB::rollback();
            return redirect()->back()->withError($e->getMessage());
        }
    }

    public function bayarAngsuranDipercepat(Request $request, $id)
    {
        // dd($request);
        try {
            $rule['besar_pembayaran'] = 'required';
            
            $validator = Validator::make($request->toArray(), $rule);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return redirect()->back()->withErrors($errors);
            }
            // dd($request);

            $pinjaman = Pinjaman::where('kode_pinjam', $id)->first();
            $listAngsuran = $pinjaman->listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS)->sortBy('angsuran_ke')->values();

            if($request->discount)
            {
                $totalDiskon = $request->discount / 100 * $pinjaman->jasaPelunasanDipercepat;
                $pinjaman->diskon = $request->discount;
                $pinjaman->total_diskon = $totalDiskon;
            }
            $pinjaman->keterangan = $request->keterangan;

            $pinjamanId = $pinjaman->id;
            $config['disk'] = 'upload';
            $config['upload_path'] = '/pinjaman/' . $pinjamanId . '/confirmationDocument';
            $config['public_path'] = env('APP_URL') . '/pinjaman/' . $id . '/confirmationDocument';
            if (!Storage::disk($config['disk'])->has($config['upload_path'])) {
                Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
            }

            if (isset($request->confirmation_document)) {
                if ($request->confirmation_document->isValid()) {
                    $filename = uniqid() . '.' . $request->confirmation_document->getClientOriginalExtension();

                    Storage::disk($config['disk'])->putFileAs($config['upload_path'], $request->confirmation_document, $filename);
                    $pinjaman->confirmation_document = $config['disk'] . $config['upload_path'] . '/' . $filename;
                }
            }

            $pinjaman->save();

            $pembayaran = 0;
            foreach ($request->besar_pembayaran as $value)
            {
                $val = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                $pembayaran = $pembayaran + $val;
            }

            $totalBayar = $request->total_bayar;
            if($request->service_fee)
            {
                $totalBayar = $totalBayar + $request->service_fee;
            }

            if ($pembayaran < $totalBayar || $pembayaran > $totalBayar) {
                return redirect()->back()->withError('Besar pembayaran harus sama dengan total bayar');
            }

            if ($request->discount && !$request->confirmation_document) {
                return redirect()->back()->withError('Dokumen konfirmasi harus disertakan');
            }

            for ($i = 0; $i < count($request->besar_pembayaran); $i++) {
                $pembayaran = filter_var($request->besar_pembayaran[$i], FILTER_SANITIZE_NUMBER_INT);
                $payment = filter_var($request->besar_pembayaran[$i], FILTER_SANITIZE_NUMBER_INT);
                if ($request->jenis_pembayaran[$i]) {
                    $kode = $request->jenis_pembayaran[$i];
                    $tabungan = Tabungan::where('kode_trans', $kode)
                        ->where('kode_anggota', $pinjaman->kode_anggota)
                        ->first();
                    $anggota = $pinjaman->anggota;

                    if ($tabungan->besar_tabungan < $pembayaran) {
                        return redirect()->back()->withError('Sisa tabungan tidak mencukupi untuk melakukan pembayaran');
                    }
                }

                foreach ($listAngsuran as $angsuran) {
                    $angsuran->besar_pembayaran = $angsuran->totalAngsuran;
                    $angsuran->id_status_angsuran = STATUS_ANGSURAN_LUNAS;
                    $angsuran->paid_at = Carbon::now();
                    $angsuran->u_entry = Auth::user()->name;
                    if ($request->jenis_pembayaran[$i]) {
                        $codeCoa = Code::where('CODE', $tabungan->kode_trans)->first();
                        $angsuran->id_akun_kredit = $codeCoa->id;
                    } else {
                        $angsuran->id_akun_kredit = ($request->id_akun_kredit[$i]) ? $request->id_akun_kredit[$i] : null;
                    }
                    $angsuran->tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
                    // $angsuran->serial_number = $serialNumber;
                    $angsuran->save();


                    // $pinjaman->sisa_angsuran = 0;
                    // $pinjaman->sisa_pinjaman = 0;
                    // $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
                    // $pinjaman->tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);

                    // $pinjaman->save();

                    // create JKM angsuran
                    // JurnalManager::createJurnalAngsuran($angsuran);
                }
                $pinjaman->tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);

                if ($request->jenis_pembayaran[$i]) {
                    $codeCoa = Code::where('CODE', $tabungan->kode_trans)->first();
                    $pinjaman->id_akun_debet = $codeCoa->id;
                } else {
                    $pinjaman->id_akun_debet = ($request->id_akun_kredit[$i]) ? $request->id_akun_kredit[$i] : null;
                }
                $pinjaman->serial_number = PinjamanManager::getSerialNumber(Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi));
                $pinjaman->save();

                if ($request->jenis_pembayaran[$i]) {
                    $penarikan = new Penarikan();
                    // get next serial number
                    $nextSerialNumber = PenarikanManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
                    $kode = $request->jenis_pembayaran[$i];
                    $tabungan = Tabungan::where('kode_trans', $kode)->first();
                    $besarPenarikan = $request->besar_pembayaran[$i];
                    $anggota = $pinjaman->anggota;
                    $user = Auth::user();

                    DB::transaction(function () use ($besarPenarikan, $anggota, $tabungan, &$penarikan, $user, $nextSerialNumber, $pinjaman) {
                        $penarikan->kode_anggota = $anggota->kode_anggota;
                        $penarikan->kode_tabungan = $tabungan->kode_tabungan;
                        $penarikan->id_tabungan = $tabungan->id;
                        $penarikan->besar_ambil = $besarPenarikan;
                        $penarikan->code_trans = $tabungan->kode_trans;
                        $penarikan->tgl_ambil = Carbon::now();
                        $penarikan->u_entry = $user->name;
                        $penarikan->created_by = $user->id;
                        $penarikan->status_pengambilan = STATUS_PENGAMBILAN_DITERIMA;
                        $penarikan->serial_number = $nextSerialNumber;
                        $penarikan->tgl_acc = Carbon::now();
                        $penarikan->tgl_transaksi = Carbon::now()->format('Y-m-d');
                        $penarikan->approved_by = $user->id;
                        $penarikan->is_pelunasan_dipercepat = 1;
                        $penarikan->paid_by_cashier = $user->id;
                        $penarikan->description = 'Pengambilan pelunasan dipercepat untuk pinjaman ' . $pinjaman->kode_pinjam;
                        $penarikan->save();
                    });

                    // JurnalManager::createJurnalPenarikan($penarikan);
                }

                //dd($angsuran);die;
                JurnalManager::createJurnalPelunasanDipercepat($pinjaman);
                $pinjaman->sisa_angsuran = 0;
                $pinjaman->sisa_pinjaman = 0;
                $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
                $pinjaman->save();
            }
            return redirect()->back()->withSuccess('berhasil melakukan pembayaran');
        } catch (\Throwable $e) {
            dd($e);
            \Log::error($e);
            $message = $e->getMessage();
            return redirect()->back()->withError('gagal melakukan pembayaran');
        }
    }

    public function editAngsuran(Request $request)
    {
        try {
            $angsuran = Angsuran::with('pinjaman')->where('kode_angsur', $request->kode_angsur)->first();
            $pembayaran = filter_var($request->besar_pembayaran, FILTER_SANITIZE_NUMBER_INT);
            $pinjaman = $angsuran->pinjaman;

            // save angsuran
            $angsuran->temp_tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
            $angsuran->updated_by = Auth::user()->id;
            $angsuran->temp_besar_pembayaran = $pembayaran;
            $angsuran->updated_at = Carbon::now();
            $angsuran->id_status_angsuran = STATUS_ANGSURAN_MENUNGGU_APPROVAL;
            $angsuran->save();

            return redirect()->back()->withSuccess('ubah data angsuran berhasil diajukan');
        } catch (\Throwable $e) {
            \Log::error($e);
            $message = $e->getMessage();
            return redirect()->back()->withError('gagal mengubah data angsuran');
        }
    }

    public function updateStatusAngsuran(Request $request)
    {
        try {
            $user = Auth::user();
            $check = Hash::check($request->password, $user->password);
            if (!$check) {
                Log::error('Wrong Password');
                return response()->json(['message' => 'Wrong Password'], 412);
            }

            $angsuran = Angsuran::with('pinjaman')->where('kode_angsur', $request->id)->first();
            $pinjaman = $angsuran->pinjaman;

            if ($request->status == STATUS_ANGSURAN_DITERIMA) {
                // save angsuran
                $angsuran->tgl_transaksi = $angsuran->temp_tgl_transaksi;
                $angsuran->besar_pembayaran = $angsuran->temp_besar_pembayaran;
                $angsuran->save();

                $angsuran = Angsuran::with('pinjaman')->where('kode_angsur', $request->id)->first();

                // set new angsuran status
                if ($angsuran->besar_pembayaran >= $angsuran->totalAngsuran) {
                    $angsuran->id_status_angsuran = STATUS_ANGSURAN_LUNAS;
                } else {
                    $angsuran->id_status_angsuran = STATUS_ANGSURAN_BELUM_LUNAS;
                    $pinjaman->sisa_angsuran = $pinjaman->sisa_angsuran + 1;
                    $pinjaman->sisa_pinjaman += $angsuran->sisaPinjaman;
                    $pinjaman->save();
                }
                $angsuran->save();

                // set status pinjaman
                if ($pinjaman->sisa_pinjaman <= 0) {
                    $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_LUNAS;
                } else {
                    $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_BELUM_LUNAS;
                }
                $pinjaman->save();

                // update jurnal
                $journals = $angsuran->jurnals;
                foreach ($journals as $key => $journal) {
                    if ($journal) {
                        if ($journal->kredit != 0) {
                            $journal->kredit = $angsuran->besar_pembayaran;
                            $journal->updated_by = Auth::user()->id;
                            $journal->save();
                        }
                    }
                }
            } elseif ($request->status == STATUS_ANGSURAN_DITOLAK) {
                $angsuran->id_status_angsuran = STATUS_PINJAMAN_LUNAS;
                $angsuran->save();
            }

            return response()->json(['message' => 'success'], 200);
        } catch (\Exception $e) {
            \Log::error($e);
            $message = $e->getMessage();
            return response()->json(['message' => $message], 500);
        }
    }

    public function create(Request $request)
    {
        $this->authorize('add pinjaman', Auth::user());
        $listJenisPinjaman = JenisPinjaman::all();

        if ($request->kode_anggota) {
            $data['anggota'] = Anggota::find($request->kode_anggota);
        }
        $data['title'] = "Add Saldo Awal";
        $data['listJenisPinjaman'] = $listJenisPinjaman;
        $data['request'] = $request;
        return view('pinjaman.create', $data);
    }

    public function store(Request $request)
    {

        // get next serial number
        $nextSerialNumber = PinjamanManager::getSerialNumber(Carbon::now()->format('d-m-Y'));

        foreach ($request->besar_pinjam as $key => $besar_pinjam) {
            if ($besar_pinjam > 0) {
                $pinjaman = new Pinjaman();
                $kodeAnggota = $request->kode_anggota;
                $kodePinjaman = str_replace('.', '', $request->kode_jenis_pinjam[$key]) . '-' . $kodeAnggota . '-' . Carbon::now()->format('dmYHis');
                $pinjaman->kode_pinjam = $kodePinjaman;
                $pinjaman->kode_pengajuan_pinjaman = $kodePinjaman;
                $pinjaman->kode_anggota = $kodeAnggota;
                $pinjaman->kode_jenis_pinjam = $request->kode_jenis_pinjam[$key];
                $pinjaman->besar_pinjam = $besar_pinjam;
                $pinjaman->besar_angsuran_pokok = $besar_pinjam / $request->lama_angsuran[$key];
                $pinjaman->lama_angsuran = $request->lama_angsuran[$key];
                $pinjaman->sisa_angsuran = $request->sisa_angsuran[$key];
                $pinjaman->sisa_pinjaman = $request->sisa_angsuran[$key] * $pinjaman->besar_angsuran_pokok;
                $pinjaman->biaya_jasa = $request->jasa[$key];
                $pinjaman->besar_angsuran = $request->jasa[$key] + $pinjaman->besar_angsuran_pokok;
                $pinjaman->biaya_asuransi = 0;
                $pinjaman->biaya_provisi = 0;
                $pinjaman->biaya_administrasi = 0;
                $pinjaman->u_entry = Auth::user()->name;
                $pinjaman->tgl_entri = Carbon::now();
                $pinjaman->tgl_tempo = Carbon::now()->addMonths($request->sisa_angsuran[$key] - 1);
                $pinjaman->id_status_pinjaman = STATUS_PINJAMAN_BELUM_LUNAS;
                $pinjaman->keterangan = 'Mutasi Saldo Awal Pinjaman';
                $pinjaman->save();
                //            dd($pinjaman);die;


                for ($i = 0; $i <= $pinjaman->sisa_angsuran - 1; $i++) {

                    // get next serial number
                    $nextSerialNumber = AngsuranManager::getSerialNumber(Carbon::now()->format('d-m-Y'));

                    $jatuhTempo = $pinjaman->tgl_entri->addMonths($i)->endOfMonth();
                    $sisaPinjaman = $pinjaman->sisa_pinjaman;
                    $angsuran = new Angsuran();
                    $angsuran->kode_pinjam = $pinjaman->kode_pinjam;
                    $angsuran->angsuran_ke = $pinjaman->lama_angsuran - $pinjaman->sisa_angsuran + $i + 1;
                    $angsuran->besar_angsuran = $pinjaman->besar_angsuran_pokok;
                    $angsuran->denda = 0;
                    $angsuran->jasa = $pinjaman->biaya_jasa;
                    $angsuran->kode_anggota = $pinjaman->kode_anggota;
                    $angsuran->sisa_pinjam = $sisaPinjaman;
                    $angsuran->tgl_entri = Carbon::now();
                    $angsuran->jatuh_tempo = $jatuhTempo;
                    $angsuran->u_entry = Auth::user()->name;
                    $angsuran->serial_number = $nextSerialNumber;
                    //                 dd($angsuran);die;
                    $angsuran->save();
                }
            }
        }
        return redirect()->route('home', ['kw_kode_anggota' => $request->kode_anggota])->withSuccess("Saldo Tersimpan");
    }

    public function importPinjaman()
    {
        $data['title'] = "Import Saldo Pinjaman";
        return view('pinjaman.importSaldo', $data);
    }
    public function importDataPinjaman()
    {
        $data['title'] = "Import Data Pinjaman";
        return view('pinjaman.importData', $data);
    }

    public function storeImportPinjaman(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Excel::import(new PinjamanImport(), $request->file);
            });
            return redirect()->back()->withSuccess('Import data berhasil');
        } catch (\Throwable $e) {
            Log::error($e);
            return redirect()->back()->withError('Gagal import data');
        }
    }
    public function storeImportDataPinjaman(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Excel::import(new PinjamanBaruImport(), $request->file);
            });
            return redirect()->back()->withSuccess('Import data berhasil');
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError($message);
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $user = Auth::user();
            $this->authorize('delete pinjaman', $user);

            // check password
            $check = Hash::check($request->pw, $user->password);
            if (!$check) {
                return response()->json(['message' => 'Wrong password'], 403);
            }

            $pinjaman = Pinjaman::where('kode_pinjam', $id)->first();
            if (is_null($pinjaman)) {
                return response()->json(['message' => 'Pinjaman not found'], 404);
            }


            $listAngsuran = $pinjaman->listAngsuran;
            foreach ($listAngsuran as $angsuran) {
                $angsuran->delete();
                if ($angsuran->jurnals()) {
                    $angsuran->jurnals()->delete();
                }
            }

            if ($pinjaman->jurnals()) {
                $pinjaman->jurnals()->delete();
            }
            if ($pinjaman->pengajuan) {
                $pinjaman->pengajuan->delete();
            }

            $pinjaman->delete();

            return response()->json(['message' => 'Delete data success'], 200);
        } catch (\Throwable $e) {
            \Log::error($e);
            return response()->json(['message' => 'Delete data gagal'], 500);
        }
    }

    public function viewDataJurnalPinjaman($id)
    {
        $pengajuan = Pengajuan::where('kode_pengajuan', $id)->first();
        // dd($pengajuan->pinjaman->jurnals);
        if ($pengajuan->pinjaman->jurnals) {
            $data['jurnals'] = $pengajuan->pinjaman->jurnals;
            return view('pinjaman.jurnal', $data);
        } else {
            $data['pengajuan'] = $pengajuan;
            return view('pinjaman.viewjurnal', $data);
        }


        // return response()->json(['message' => 'error'], 500);
    }
    public function viewDataCoaBank($id)
    {
        $pengajuan = Pengajuan::where('kode_pengajuan', $id)->first();
        // dd($pengajuan->pinjaman->jurnals);
        // if ($pengajuan->pinjaman->jurnals){
        $kode = $pengajuan->pinjaman;
        $code = Code::find($kode->id_akun_kredit);

        $raw = $pengajuan->pinjaman->jurnals;
        $data = $raw->where('akun_kredit', $code->CODE)->first();
        // dd($data);
        // dd($raw->where('akun_kredit',$code->CODE));
        // return view('pinjaman.jurnal', $data);
        // }


        return response()->json($data, 200);
    }

    public function storeDataCoaBank(Request $request, $id)
    {
        try {
            // dd($request);
            $pengajuan = Pengajuan::where('kode_pengajuan', $id)->first();
            $pinjaman = $pengajuan->pinjaman;
            $pinjaman->id_akun_kredit = $request->id_akun_debet;
            $pinjaman->save();
            $coa = Code::find($request->id_akun_debet);
            $jurnal = Jurnal::find($request->id_jurnal);
            $jurnal->akun_kredit = $coa->CODE;
            $jurnal->save();
            return response()->json(['message' => 'Update Coa success'], 200);
        } catch (\Throwable $e) {
            \Log::error($e);
            return response()->json(['message' => 'Update Coa gagal'], 500);
        }
    }

    public function exportSaldoAwalPinjaman()
    {
        $user = Auth::user();
        $this->authorize('view saldo awal', $user);

        $filename = 'export_saldo_awal_pinjaman_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
        return Excel::download(new SaldoAwalPinjamanExport(), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function searchPinjamanAnggota($kode_anggota, Request $request)
    {
        $query = Pinjaman::where('kode_anggota', $kode_anggota);
        if ($request->jenisPinjaman == KATEGORI_JENIS_PINJAMAN_JANGKA_PANJANG) {
            $query = $query->japan();
        } elseif ($request->jenisPinjaman == KATEGORI_JENIS_PINJAMAN_JANGKA_PENDEK) {
            $query = $query->japen();
        }
        $query = $query->join('t_jenis_pinjam', 't_pinjam.kode_jenis_pinjam', 't_jenis_pinjam.kode_jenis_pinjam')
            ->where('id_status_pinjaman', STATUS_PINJAMAN_BELUM_LUNAS)
            ->select('kode_pinjam', 'nama_pinjaman')
            ->get();

        return $query;
    }
    public function updatesaldoawal(Request $request)
    {
        $user = Auth::user();
        $role = $user->roles->first();
        $this->authorize('edit saldo awal pinjaman', $user);
        try {
            $pinjam = Pinjaman::where('kode_pinjam', $request->kode_pinjam)->first();

            $nominal = filter_var($request->saldo_mutasi, FILTER_SANITIZE_NUMBER_INT);

            if ($pinjam) {
                $pinjam->saldo_mutasi = $nominal;
                $pinjam->save();
                return response()->json(['message' => 'Edit data success', 'status' => true], 200);
            } else {
                return response()->json(['message' => 'Edit failed', 'status' => false], 404);
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Terjadi Kesalahan', 'status' => false], 500);
        }
    }

    public function report(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view jurnal', $user);

        // data collection
        $reports = collect();

        $today = Carbon::today();

        // period
        // check if period date has been selected
        if (!$request->period) {
            $request->period = Carbon::today()->format('Y');
        }

        // get start and end of year
        $startOfYear = Carbon::createFromFormat('Y', $request->period)->startOfYear()->toDateTimeString();
        $endOfYear   = Carbon::createFromFormat('Y', $request->period)->endOfYear()->toDateTimeString();

        $pinjamanJapens = Pinjaman::whereBetween('tgl_entri', [$startOfYear, $endOfYear])
            ->orderBy('tgl_entri')
            ->japen()
            ->get()
            ->groupBy(function ($query) {
                return Carbon::parse($query->tgl_entri)->format('m');
            });

        $pinjamanJapans = Pinjaman::whereBetween('tgl_entri', [$startOfYear, $endOfYear])
            ->orderBy('tgl_entri')
            ->japan()
            ->get()
            ->groupBy(function ($query) {
                return Carbon::parse($query->tgl_entri)->format('m');
            });

        $totalJapenDiterima = 0;
        $totalJapenApproved = 0;
        $totalJapanDiterima = 0;
        $totalJapanApproved = 0;
        $totalJapanTrx = 0;
        $totalJapenTrx = 0;

        // loop for every month in year
        for ($i = 1; $i <= 12; $i++) {
            $japenDiterima = 0;
            $japenApproved = 0;
            $japanDiterima = 0;
            $japanApproved = 0;
            $japenTemp = [];
            $japanTemp = [];

            if ($i < 10) {
                if (property_exists((object)$pinjamanJapens->toArray(), '0' . $i)) {
                    $japenTemp = $pinjamanJapens['0' . $i];
                }

                if (property_exists((object)$pinjamanJapans->toArray(), '0' . $i)) {
                    $japanTemp = $pinjamanJapans['0' . $i];
                }
            } else {
                if (property_exists((object)$pinjamanJapens->toArray(), $i)) {
                    $japenTemp = $pinjamanJapens[$i];
                }

                if (property_exists((object)$pinjamanJapans->toArray(), $i)) {
                    $japanTemp = $pinjamanJapans[$i];
                }
            }

            $trxJapen = count($japenTemp);
            $trxJapan = count($japanTemp);

            foreach ($japenTemp as $japen) {
                if ($japen->pengajuan) {
                    if ($japen->pengajuan->bukti_pembayaran == null) {
                        $japenApproved += (int)$japen->besar_pinjam;
                    } else {
                        $japenDiterima += (int)$japen->besar_pinjam;
                    }
                } else {
                    $japenDiterima += (int)$japen->besar_pinjam;
                }
            }

            foreach ($japanTemp as $japan) {
                if ($japan->pengajuan) {
                    if ($japan->pengajuan->bukti_pembayaran == null) {
                        $japanApproved += (int)$japan->besar_pinjam;
                    } else {
                        $japanDiterima += (int)$japan->besar_pinjam;
                    }
                } else {
                    $japanDiterima += (int)$japan->besar_pinjam;
                }
            }

            $reports->put($i, [
                'trxJapen' => $trxJapen,
                'trxJapan' => $trxJapan,
                'japenDiterima' => $japenDiterima,
                'japenApproved' => $japenApproved,
                'japanDiterima' => $japanDiterima,
                'japanApproved' => $japanApproved
            ]);

            // total data
            $totalJapanTrx += $trxJapan;
            $totalJapenTrx += $trxJapen;
            $totalJapenApproved += $japenApproved;
            $totalJapanApproved += $japanApproved;
            $totalJapanDiterima += $japanDiterima;
            $totalJapenDiterima += $japenDiterima;
        }

        $data['totalJapanTrx'] = $totalJapanTrx;
        $data['totalJapenTrx'] = $totalJapenTrx;
        $data['totalJapenApproved'] = $totalJapenApproved;
        $data['totalJapanApproved'] = $totalJapanApproved;
        $data['totalJapanDiterima'] = $totalJapanDiterima;
        $data['totalJapenDiterima'] = $totalJapenDiterima;
        $data['request'] = $request;

        $data['title'] = "Laporan Pinjaman";
        $data['reports'] = $reports;
        return view('pinjaman.report', $data);
    }

    public function createExcelReport(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try {
            if($request->pdf)
            {
                // data collection
                $reports = collect();

                $today = Carbon::today();

                // period
                // check if period date has been selected
                if(!$request->period)
                {
                    $request->period = Carbon::today()->format('Y');
                }

                // get start and end of year
                $startOfYear = Carbon::createFromFormat('Y', $request->period)->startOfYear()->toDateTimeString();
                $endOfYear   = Carbon::createFromFormat('Y', $request->period)->endOfYear()->toDateTimeString();

                $pinjamanJapens = Pinjaman::whereBetween('tgl_entri', [$startOfYear, $endOfYear])
                                        ->orderBy('tgl_entri')
                                        ->japen()
                                        ->get()
                                        ->groupBy(function($query) {
                                            return Carbon::parse($query->tgl_entri)->format('m');
                                        });

                $pinjamanJapans = Pinjaman::whereBetween('tgl_entri', [$startOfYear, $endOfYear])
                                        ->orderBy('tgl_entri')
                                        ->japan()
                                        ->get()
                                        ->groupBy(function($query) {
                                            return Carbon::parse($query->tgl_entri)->format('m');
                                        });

                $totalJapenDiterima = 0;
                $totalJapenApproved = 0;
                $totalJapanDiterima = 0;
                $totalJapanApproved = 0;
                $totalJapanTrx = 0;
                $totalJapenTrx = 0;

                // loop for every month in year
                for ($i=1; $i <=12 ; $i++)
                {
                    $japenDiterima = 0;
                    $japenApproved = 0;
                    $japanDiterima = 0;
                    $japanApproved = 0;
                    $japenTemp = [];
                    $japanTemp = [];

                    if($i < 10)
                    {
                        if(property_exists((object)$pinjamanJapens->toArray(), '0' . $i))
                        {

                            $japenTemp = $pinjamanJapens['0' . $i];
                        }

                        if(property_exists((object)$pinjamanJapans->toArray(), '0' . $i))
                        {
                            $japanTemp = $pinjamanJapans['0' . $i];
                        }
                    }
                    else
                    {
                        if(property_exists((object)$pinjamanJapens->toArray(), $i))
                        {
                            $japenTemp = $pinjamanJapens[$i];
                        }

                        if(property_exists((object)$pinjamanJapans->toArray(), $i))
                        {
                            $japanTemp = $pinjamanJapans[$i];
                        }
                    }

                    $trxJapen = count($japenTemp);
                    $trxJapan = count($japanTemp);

                    foreach($japenTemp as $japen)
                    {
                        if($japen->pengajuan)
                        {
                            if($japen->pengajuan->bukti_pembayaran == null)
                            {
                                $japenApproved += (int)$japen->besar_pinjam;
                            }
                            else
                            {
                                $japenDiterima += (int)$japen->besar_pinjam;
                            }
                        }
                        else
                        {
                            $japenDiterima += (int)$japen->besar_pinjam;
                        }
                    }

                    foreach($japanTemp as $japan)
                    {
                        if($japan->pengajuan)
                        {
                            if($japan->pengajuan->bukti_pembayaran == null)
                            {
                                $japanApproved += (int)$japan->besar_pinjam;
                            }
                            else
                            {
                                $japanDiterima += (int)$japan->besar_pinjam;
                            }
                        }
                        else
                        {
                            $japanDiterima += (int)$japan->besar_pinjam;
                        }
                    }

                    $reports->put($i, ['trxJapen' => $trxJapen,
                                            'trxJapan' => $trxJapan,
                                            'japenDiterima' => $japenDiterima,
                                            'japenApproved' => $japenApproved,
                                            'japanDiterima' => $japanDiterima,
                                            'japanApproved' => $japanApproved
                                        ]);

                    // total data
                    $totalJapanTrx += $trxJapan;
                    $totalJapenTrx += $trxJapen;
                    $totalJapenApproved += $japenApproved;
                    $totalJapanApproved += $japanApproved;
                    $totalJapanDiterima += $japanDiterima;
                    $totalJapenDiterima += $japenDiterima;
                }

                $data = [
                    'reports' => $reports,
                    'totalJapanTrx' => $totalJapanTrx,
                    'totalJapenTrx' => $totalJapenTrx,
                    'totalJapenApproved' => $totalJapenApproved,
                    'totalJapanApproved' => $totalJapanApproved,
                    'totalJapanDiterima' => $totalJapanDiterima,
                    'totalJapenDiterima' => $totalJapenDiterima,
                    'request' => $request,
                ];

                // pinjaman.reportExcel
                // share data to view
                view()->share('data',$data);
                $pdf = PDF::loadView('pinjaman.reportExcel', $data)->setPaper('a4', 'landscape');

                // download PDF file with download method
                $filename = 'export_pinjaman_report_excel_' . Carbon::now()->format('d M Y') . '.pdf';
                return $pdf->download($filename);
            }
            $filename = 'export_pinjaman_report_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
            return Excel::download(new LaporanPinjamanExcelExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
        } catch (\Throwable $e) {
            Log::error($e);
            dd($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function createExcelPengajuanPinjaman(Request $request)
    {
        try {
            $user = Auth::user();
            $listPengajuanPinjaman = Pengajuan::with('anggota', 'createdBy', 'approvedBy', 'pinjaman', 'paidByCashier', 'jenisPinjaman', 'statusPengajuan', 'pengajuanTopup', 'akunDebet', 'jenisPenghasilan');

            if ($request->status_pengajuan != "") {
                $listPengajuanPinjaman = $listPengajuanPinjaman->where('id_status_pengajuan', $request->status_pengajuan);
            } else {
                $listPengajuanPinjaman = $listPengajuanPinjaman->whereNotIn('id_status_pengajuan', [8, 9, 10]);
            }

            if ($request->start_tgl_pengajuan != "") {
                $tgl_pengajuan = Carbon::createFromFormat('d-m-Y', $request->start_tgl_pengajuan);
                $listPengajuanPinjaman = $listPengajuanPinjaman->where('tgl_pengajuan', '>=', $tgl_pengajuan);
            }

            if ($request->end_tgl_pengajuan != "") {
                $tgl_pengajuan = Carbon::createFromFormat('d-m-Y', $request->end_tgl_pengajuan);
                $listPengajuanPinjaman = $listPengajuanPinjaman->where('tgl_pengajuan', '<=', $tgl_pengajuan);
            }

            if ($request->anggota != "") {
                $listPengajuanPinjaman = $listPengajuanPinjaman->where('kode_anggota', $request->anggota);
            }

            if ($user->isAnggota()) {
                $anggota = $user->anggota;
                if (is_null($anggota)) {
                    return redirect()->back()->withError('Your account has no members');
                }

                $listPengajuanPinjaman = $listPengajuanPinjaman->where('kode_anggota', $anggota->kode_anggota);
            }

            $listPengajuanPinjaman = $listPengajuanPinjaman->get();
            $data['listPengajuanPinjaman'] = $listPengajuanPinjaman;

            $filename = 'pengajuan-excel-' . Carbon::now() . '.xlsx';
            // return view('pinjaman.pengajuan.excel', $data);
            return Excel::download(new PengajuanPinjamanExport($data), $filename);
        } catch (\Throwable $th) {
            $message = $th->getMessage() . ' || ' . $th->getFile() . ' || ' . $th->getLine();
        }
    }

    public function setDiscount(Request $request, $id)
    {
        // set discount to pinjaman
        $pinjaman = Pinjaman::where('kode_pinjam', $id)->first();
        $totalDiskon = $request->discount / 100 * $pinjaman->biaya_jasa;
        $pinjaman->diskon = $request->discount;
        $pinjaman->total_diskon = $request->discount / 100 * $pinjaman->biaya_jasa;
        $pinjaman->besar_angsuran = $pinjaman->besar_angsuran_pokok + $pinjaman->biaya_jasa - $totalDiskon;
        $pinjaman->save();

        // generate discount to angsuran
        $listAngsuran = $pinjaman->listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS);
        foreach ($listAngsuran as $angsuran) {
            $angsuran->diskon = $pinjaman->total_diskon;
            $angsuran->save();
        }
        return redirect()->back()->withSuccess('Diskon berhasil disimpan');
    }

    public function edit(Request $request)
    {
        $this->authorize('edit pinjaman', Auth::user());
        $pinjaman = Pinjaman::where('kode_pinjam', $request->id)->first();
        $listJenisPinjaman = JenisPinjaman::pluck('nama_pinjaman', 'kode_jenis_pinjam');
        $data['anggota'] = Anggota::find($pinjaman->kode_anggota);
        $data['title'] = "Add Saldo Awal";
        $data['listJenisPinjaman'] = $listJenisPinjaman;
        $data['title'] = 'Edit Pinjaman';
        $data['pinjaman'] = $pinjaman;
        $data['listAngsuran'] = $pinjaman->listAngsuran;
        return view('pinjaman.edit', $data);
    }

    public function update(Request $request)
    {
        $this->authorize('edit pinjaman', Auth::user());
        if (isset($request->sub)) {
            switch ($request->sub) {
                case 'submit':
                    $buffer = $this->updateSubmit($request);
                    if ($buffer) {
                        return redirect()->back()->withSuccess('Data Berhasil disimpan');;
                    }
                    break;
                case 'posting':
                    $buffer = $this->updatePosting($request);
                    if ($buffer) {
                        return redirect()->back()->withSuccess('Jurnal Berhasil DiUpdate');;
                    }
                    break;
                default:
                    break;
            }

            return redirect()->back()->withError('Data Gagal disimpan');;
        }
    }

    public function updatePosting(Request $request)
    {
        $id_akun_kredit = [];



        if (isset($request->kode_angsur)) {
            foreach ($request->kode_angsur as $key => $val) {
                if (isset($request->edit_id_akun_kredit[$key])) {
                    $code = Code::where('CODE', $request->edit_id_akun_kredit[$key])->first();
                    $baris = $key + 1;
                    if (!$code) {
                        return redirect()->back()->withError('COA ' . $request->edit_id_akun_kredit[$key] . ' pada angsuran baris ke ' . $baris . ' tidak ada dalam database');
                    }
                    $edit_id_akun_kredit[$key] = $code->id;
                } else {
                    $edit_id_akun_kredit[$key] = null;
                }

                $angsuran =  Angsuran::findOrFail($val);
                if ($angsuran->jurnals->count() > 0) {
                    if (isset($edit_id_akun_kredit[$key])) {
                        $angsuran->jurnals()->delete();

                        // JurnalManager::createJurnalAngsuran($angsuran);
                    } else {
                        $angsuran->serial_number = null;
                        $angsuran->id_akun_kredit = null;
                        $angsuran->save();
                        $angsuran->jurnals()->delete();
                    }
                } else {
                    if (isset($edit_id_akun_kredit[$key])) {
                        $serialNumber = AngsuranManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
                        $angsuran->serial_number = $serialNumber;
                        $angsuran->save();

                        // JurnalManager::createJurnalAngsuran($angsuran);
                    }
                }
                if ($edit_id_akun_kredit[$key] !== null) {
                    AngsuranPartialManager::generateFromEdit($angsuran);
                } else {
                    if ($angsuran->angsuranPartial) {
                        foreach ($angsuran->angsuranPartial as $angsp) {
                            $angsp->jurnals()->delete();
                        }

                        $angsuran->angsuranPartial()->delete();
                    }
                    $angsuran->serial_number = null;
                    $angsuran->save();
                }
            }
            return true;
        }



        return false;
    }

    public function updateSubmit(Request $request)
    {
        $id_akun_kredit = [];
        $pinjaman = Pinjaman::where('kode_pinjam', $request->kode_pinjam)->first();
        if (isset($request->angsuran_ke)) {
            foreach ($request->angsuran_ke as $key => $val) {
                if (isset($request->id_akun_kredit[$key])) {
                    $code = Code::where('CODE', $request->id_akun_kredit[$key])->first();
                    $baris = $key + 1;
                    if (!$code) {
                        return redirect()->back()->withError('COA ' . $request->id_akun_kredit[$key] . ' pada angsuran baris ke ' . $baris . ' tidak ada dalam database');
                    }
                    $id_akun_kredit[$key] = $code->id;
                } else {
                    $id_akun_kredit[$key] = null;
                }
            }
        }
        if (isset($request->kode_angsur)) {
            foreach ($request->kode_angsur as $key => $val) {
                if (isset($request->edit_id_akun_kredit[$key])) {
                    $code = Code::where('CODE', $request->edit_id_akun_kredit[$key])->first();
                    $baris = $key + 1;
                    if (!$code) {
                        return redirect()->back()->withError('COA ' . $request->edit_id_akun_kredit[$key] . ' pada angsuran baris ke ' . $baris . ' tidak ada dalam database');
                    }
                    $edit_id_akun_kredit[$key] = $code->id;
                } else {
                    $edit_id_akun_kredit[$key] = null;
                }
            }
        }

        $fieldpinjam = [
            'kode_anggota' => $request->kode_anggota,
            'kode_jenis_pinjam' => $request->kode_jenis_pinjam,
            'besar_pinjam' => filter_var($request->besar_pinjam, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND),
            'sisa_pinjaman' => filter_var($request->sisa_pinjaman, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND),
            'biaya_asuransi' => filter_var($request->biaya_asuransi, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND),
            'biaya_provisi' => filter_var($request->biaya_provisi, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND),
            'biaya_administrasi' => filter_var($request->biaya_administrasi, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND),
            'id_status_pinjaman' => $request->id_status_pinjaman,
        ];
        // if ($pinjaman->listAngsuran->count()>0){
        //    foreach ($pinjaman->listAngsuran as $angs){
        //     $angs->delete();
        //    }
        // }
        if (isset($request->kode_angsur)) {
            foreach ($request->kode_angsur as $key => $val) {
                $angsuran =  Angsuran::findOrFail($val);
                $angsuran->sisa_pinjam = filter_var($request->sisa_pinjaman, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $angsuran->angsuran_ke = $request->edit_angsuran_ke[$key];
                $angsuran->besar_angsuran = filter_var($request->edit_besar_angsuran[$key], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $angsuran->jasa = filter_var($request->edit_jasa[$key], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $angsuran->u_entry = Auth::user()->name;
                $angsuran->jatuh_tempo = $request->edit_jatuh_tempo[$key];
                $angsuran->besar_pembayaran = filter_var($request->edit_besar_pembayaran[$key], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $angsuran->tgl_transaksi = $request->edit_tanggal_pembayaran[$key];
                $angsuran->tgl_entri = Carbon::now();;
                $angsuran->paid_at = $request->edit_tanggal_pembayaran[$key];
                $angsuran->id_akun_kredit = $edit_id_akun_kredit[$key];
                $angsuran->id_status_angsuran = $request->edit_id_status_angsuran[$key];
                $angsuran->serial_number = $request->edit_serial_number[$key];
                $angsuran->save();
                if ($angsuran->angsuranPartial) {
                    foreach ($angsuran->angsuranPartial as $angspar) {
                        $angspar->delete();
                    }
                }
                AngsuranPartialManager::generatetanpaposting($angsuran);
            }
        }

        if (isset($request->angsuran_ke)) {
            foreach ($request->angsuran_ke as $key => $val) {
                $angsuran =  new Angsuran();
                $angsuran->kode_pinjam = $request->kode_pinjam;
                $angsuran->sisa_pinjam = filter_var($request->sisa_pinjaman, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $angsuran->angsuran_ke = $val;
                $angsuran->besar_angsuran = filter_var($request->besar_angsuran[$key], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $angsuran->jasa = filter_var($request->jasa[$key], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $angsuran->u_entry = Auth::user()->name;
                $angsuran->jatuh_tempo = $request->jatuh_tempo[$key];
                $angsuran->besar_pembayaran = filter_var(($request->besar_pembayaran[$key] !== null) ? $request->besar_pembayaran[$key] : 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $angsuran->tgl_transaksi = $request->tanggal_pembayaran[$key];
                $angsuran->tgl_entri = Carbon::now();;
                $angsuran->paid_at = $request->tanggal_pembayaran[$key];
                $angsuran->id_akun_kredit = $id_akun_kredit[$key];
                $angsuran->id_status_angsuran = $request->id_status_angsuran[$key];
                $angsuran->serial_number = $request->serial_number[$key];
                $angsuran->save();
                if ($angsuran->angsuranPartial) {
                    foreach ($angsuran->angsuranPartial as $angspar) {
                        $angspar->delete();
                    }
                }
                AngsuranPartialManager::generatetanpaposting($angsuran);
            }
        }




        if ($pinjaman->update($fieldpinjam)) {
            return true;
        }

        return false;
    }
}
