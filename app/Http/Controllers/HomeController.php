<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\View\ViewSaldo;
use App\Models\Pinjaman;

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
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
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
            $data['saldo'] = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
            $data['listPinjaman'] = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                                        ->where('status', 'belum lunas')
                                        ->get();
        }
        else
        {
            $anggota = DB::table('t_anggota')->where('status', 'aktif')->count();
            $data['anggota']=$anggota;
        }
        $data['role'] = $role;
        return view('home', $data);
    }
}
