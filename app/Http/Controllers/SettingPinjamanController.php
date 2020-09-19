<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingPinjamanController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $pinjaman = DB::table('t_jenis_pinjam')
                ->get();
        $data['pinjaman'] = $pinjaman;
        return view('/setting/pinjaman/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('/setting/pinjaman/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'kode_jenis_pinjam' => 'required',
            'nama_pinjaman' => 'required',
            'lama_angsuran' => 'required',
            'maks_pinjam' => 'required',
            'bunga' => 'required',
            'u_entry' => 'required',
            'tgl_entri' => 'required',
        ]);
        
        DB::table('t_jenis_pinjam')->insert([
            'kode_jenis_pinjam', $request->kode_jenis_pinjam,
            'nama_pinjaman' => $request->nama_pinjaman,
            'lama_angsuran' => $request->lama_angsuran,
            'maks_pinjam' => $request->maks_pinjam,
            'bunga' => $request->bunga,
            'u_entry' => $request->u_entry,
            'tgl_entri' => $request->tgl_entri,
        ]);
        return redirect('/setting/pinjaman')->with('status', 'Data Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
         $pinjaman = DB::table('t_jenis_pinjam')->where('kode_jenis_pinjam', $id)->first();
        return view('/setting/pinjaman/edit', ['pinjaman' => $pinjaman]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        DB::table('t_jenis_pinjam')
                ->where('kode_jenis_pinjam', $request->kode_jenis_pinjam)
                ->update([
                    'nama_pinjaman' => $request->nama_pinjaman,
                    'lama_angsuran' => $request->lama_angsuran,
                    'maks_pinjam' => $request->maks_pinjam,
                    'bunga' => $request->bunga,
                    'u_entry' => $request->u_entry,
                    'tgl_entri' => $request->tgl_entri,
        ]);
        return redirect('/setting/pinjaman')->with('status', 'Data Berhasil Di Update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        DB::table('t_jenis_pinjam')->where('kode_jenis_pinjam', '=', $id)->delete();
        return redirect('/setting/pinjaman')->with('status', 'Data Berhasil Dihapus');
    }

}
