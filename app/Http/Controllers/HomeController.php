<?php

namespace App\Http\Controllers;

use App\Managers\AngsuranManager;
use App\Managers\PenarikanManager;
use App\Managers\PinjamanManager;
use App\Managers\SimpananManager;
use App\Managers\TabunganManager;
use App\Models\Anggota;
use App\Models\Penarikan;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\View\ViewSaldo;
use App\Models\Pinjaman;
use App\Models\Tabungan;
use App\Models\TransferredSHU;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check');
//        $this->middleware('pinjaman');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->roles->first();
        if (is_null($role))
        {
            abort(403);
        }
        if ($role->id == ROLE_ANGGOTA)
        {
            $anggota = $user->anggota;
            $data['saldo'] = TabunganManager::getSaldoTabungan($anggota->kode_anggota,Carbon::now())->sum('besar_tabungan');//  ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
            $data['listPinjaman'] = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
//                                        ->wherenotnull('mutasi_juli')
                                        ->where('id_status_pinjaman', STATUS_PINJAMAN_BELUM_LUNAS)
                                        ->get();

            $data['sisaPinjaman'] = PinjamanManager::getTotalPinjaman($anggota->kode_anggota)-AngsuranManager::getTotalAngsuran($anggota->kode_anggota);;
            $data['transferredShu'] = TransferredSHU::where('kode_anggota', $anggota->kode_anggota)->sum('amount');
            $data['anggota']=$anggota;
        }
        else
        {
            $anggota = DB::table('t_anggota')->where('status', 'aktif')->count();
            $data['anggota']=$anggota;
            $Simpanan = SimpananManager::getTotalSimpanan();
            $penarikan = PenarikanManager::getTotalPenarikan();
            $data['simpanan']=$Simpanan-$penarikan;
            $data['sisaPinjaman'] = PinjamanManager::getTotalPinjaman()-AngsuranManager::getTotalAngsuran();

            // if search
            if ($request->search)
            {
                $result = Anggota::where('kode_anggota',$request->kw_kode_anggota)
                                    ->orWhere('nama_anggota','like', '%'.$request->kw_kode_anggota.'%')
                                    ->first();
                if(is_null($result))
                {
                    return redirect()->back()->withError('Anggota tidak ditemukan');
                }

                $result->tabungan = TabunganManager::getSaldoTabungan($request->kw_kode_anggota,Carbon::now());
                $result->pinjaman = Pinjaman::where('kode_anggota',$result->kode_anggota)->get();
                $result->sumtabungan = TabunganManager::getSaldoTabungan($request->kw_kode_anggota,Carbon::now())->sum('besar_tabungan');

                $data['searchResult'] = $result;
            }
        }

        $data['role'] = $role;
        return view('home', $data);
    }
}
