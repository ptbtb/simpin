<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
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
        $this->middleware('check');
        $this->middleware('pinjaman');
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
            $data['saldo'] = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
            $data['listPinjaman'] = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                                        ->where('status', 'belum lunas')
                                        ->get();
        }
        else
        {
            $anggota = DB::table('t_anggota')->where('status', 'aktif')->count();
            $data['anggota']=$anggota;

            // if search
            if ($request->search)
            {
                $result = Anggota::find($request->kw_kode_anggota);
                if(is_null($result))
                {
                    return redirect()->back()->withError('Anggota tidak ditemukan');
                }
                $data['searchResult'] = $result;
            }
        }

        $data['role'] = $role;
        return view('home', $data);
    }
}
