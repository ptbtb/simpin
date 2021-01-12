<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\JenisPinjaman;
use App\Models\TipeJenisPinjaman;
use App\Models\KategoriJenisPinjaman;

use Auth;
use Carbon\Carbon;

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
        $this->authorize('view jenis pinjaman', Auth::user());
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
        $this->authorize('add jenis pinjaman', Auth::user());
        
        $tipeJenisPinjaman = TipeJenisPinjaman::get();
        $kategoriJenisPinjaman = KategoriJenisPinjaman::get();

        $data['title'] = 'Create Jenis Pinjaman';
        $data['tipe_jenis_pinjaman'] = $tipeJenisPinjaman;
        $data['kategori_jenis_pinjaman'] = $kategoriJenisPinjaman;
        
        return view('setting.pinjaman.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->authorize('add jenis pinjaman', Auth::user());
        try {
            $jenisPinjaman = new JenisPinjaman();
            $jenisPinjaman->kode_jenis_pinjam = $request->kode_jenis_pinjam;
            $jenisPinjaman->tipe_jenis_pinjaman_id = $request->tipe_jenis_pinjaman_id;
            $jenisPinjaman->kategori_jenis_pinjaman_id = $request->kategori_jenis_pinjaman_id;
            $jenisPinjaman->nama_pinjaman = $request->nama_pinjaman;
            $jenisPinjaman->lama_angsuran = $request->lama_angsuran;
            $jenisPinjaman->maks_pinjam = $request->maks_pinjam;
            $jenisPinjaman->bunga = $request->bunga;
            $jenisPinjaman->asuransi = $request->asuransi;
            $jenisPinjaman->biaya_admin = filter_var($request->biaya_admin, FILTER_SANITIZE_NUMBER_INT);
            $jenisPinjaman->provisi = $request->provisi;
            $jenisPinjaman->jasa = $request->jasa;
            $jenisPinjaman->jasa_pelunasan_dipercepat = $request->jasa_pelunasan_dipercepat;
            $jenisPinjaman->minimal_angsur_pelunasan = $request->minimal_angsur_pelunasan;
            $jenisPinjaman->u_entry = $request->u_entry;
            $jenisPinjaman->tgl_entri = Carbon::now();
            // dd($request);
            $jenisPinjaman->save();
            
            return redirect()->route('jenis-pinjaman-list')->withSuccess('Create Jenis Pinjaman Success');
            
        } catch (\Exception $e) {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
                $message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
        }
        
        // $this->validate($request, [
        //     'kode_jenis_pinjam' => 'required',
        //     'nama_pinjaman' => 'required',
        //     'lama_angsuran' => 'required',
        //     'maks_pinjam' => 'required',
        //     'bunga' => 'required',
        //     'u_entry' => 'required',
        //     'tgl_entri' => 'required',
        // ]);
        
        // DB::table('t_jenis_pinjam')->insert([
        //     'kode_jenis_pinjam', $request->kode_jenis_pinjam,
        //     'nama_pinjaman' => $request->nama_pinjaman,
        //     'lama_angsuran' => $request->lama_angsuran,
        //     'maks_pinjam' => $request->maks_pinjam,
        //     'bunga' => $request->bunga,
        //     'u_entry' => $request->u_entry,
        //     'tgl_entri' => $request->tgl_entri,
        // ]);
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
        $this->authorize('edit jenis pinjaman', Auth::user());

        $pinjaman = DB::table('t_jenis_pinjam')->where('kode_jenis_pinjam', $id)->first();
        $tipeJenisPinjaman = TipeJenisPinjaman::get();
        $kategoriJenisPinjaman = KategoriJenisPinjaman::get();

        $data['pinjaman'] = $pinjaman;
        $data['title'] = 'Edit Jenis Pinjaman';
        $data['tipe_jenis_pinjaman'] = $tipeJenisPinjaman;
        $data['kategori_jenis_pinjaman'] = $kategoriJenisPinjaman;

        return view('setting.pinjaman.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request) {
        $this->authorize('edit jenis pinjaman', Auth::user());
        $jenisPinjaman = JenisPinjaman::find($request->kode_jenis_pinjam);

        if (is_null($jenisPinjaman)) {
            return redirect()->back()->withError('Jenis Pinjaman not found');
        }

        try {
            $jenisPinjaman->kode_jenis_pinjam = $request->kode_jenis_pinjam;
            $jenisPinjaman->tipe_jenis_pinjaman_id = $request->tipe_jenis_pinjaman_id;
            $jenisPinjaman->kategori_jenis_pinjaman_id = $request->kategori_jenis_pinjaman_id;
            $jenisPinjaman->nama_pinjaman = $request->nama_pinjaman;
            $jenisPinjaman->lama_angsuran = $request->lama_angsuran;
            $jenisPinjaman->maks_pinjam = $request->maks_pinjam;
            $jenisPinjaman->bunga = $request->bunga;
            $jenisPinjaman->asuransi = $request->asuransi;
            $jenisPinjaman->biaya_admin = filter_var($request->biaya_admin, FILTER_SANITIZE_NUMBER_INT);
            $jenisPinjaman->provisi = $request->provisi;
            $jenisPinjaman->jasa = $request->jasa;
            $jenisPinjaman->jasa_pelunasan_dipercepat = $request->jasa_pelunasan_dipercepat;
            $jenisPinjaman->minimal_angsur_pelunasan = $request->minimal_angsur_pelunasan;
            $jenisPinjaman->u_entry = $request->u_entry;
            $jenisPinjaman->tgl_entri = Carbon::now();
            $jenisPinjaman->save();
            // dd($jenisPinjaman);
            return redirect()->route('jenis-pinjaman-list')->withSuccess('Update Jenis Pinjaman Success');
            
        } catch (\Exception $e) {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
                $message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $this->authorize('delete jenis pinjaman', Auth::user());
        DB::table('t_jenis_pinjam')->where('kode_jenis_pinjam', '=', $id)->delete();
        return redirect('/setting/pinjaman')->with('status', 'Data Berhasil Dihapus');
    }

}
