<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Auth;
class SettingSimpananController extends Controller
{
     public function __construct() {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        $this->authorize('view jenis simpanan', Auth::user());
        $simpanans = DB::table('t_jenis_simpan')
                ->get();
        $data['simpanans'] = $simpanans;
        return view('/setting/simpanan/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add jenis simpanan', Auth::user());
       return view('/setting/simpanan/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('add jenis simpanan', Auth::user());
        $this -> validate($request, [
        'kode_jenis_simpan' => 'required',
        'nama_simpanan' => 'required',
        'besar_simpanan' => 'required',
        'tgl_tagih' => 'required',
        'hari_jatuh_tempo' => 'required',
        'u_entry' => 'required',
        'tgl_entri' => 'required',
        
    ]);
        DB::table('t_jenis_simpan') -> insert([
        'kode_jenis_simpan' => $request -> kode_jenis_simpan,
        'nama_simpanan' => $request -> nama_simpanan,
        'besar_simpanan' => $request -> besar_simpanan,
        'tgl_tagih' => $request -> tgl_tagih,
        'hari_jatuh_tempo' => $request -> hari_jatuh_tempo,
        'u_entry' => $request -> u_entry,
        'tgl_entri' => $request -> tgl_entri,
        
    ]);
        return redirect('/setting/simpanan') -> with('status', 'Data Berhasil Ditambahkan');
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
        $this->authorize('edit jenis simpanan', Auth::user());
         $simpanan = DB::table('t_jenis_simpan')->where('kode_jenis_simpan', $id)->first();
        return view('/setting/simpanan/edit', ['simpanan' => $simpanan]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->authorize('edit jenis simpanan', Auth::user());
         DB::table('t_jenis_simpan') 
                 ->where('kode_jenis_simpan', $request -> kode_jenis_simpan)
                -> update([
        'nama_simpanan' => $request -> nama_simpanan,
        'besar_simpanan' => $request -> besar_simpanan,
        'tgl_tagih' => $request -> tgl_tagih,
        'hari_jatuh_tempo' => $request -> hari_jatuh_tempo,
        'u_entry' => $request -> u_entry,
        'tgl_entri' => $request -> tgl_entri,
        
    ]);
        return redirect('/setting/simpanan') -> with('status', 'Data Berhasil Di Update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete jenis simpanan', Auth::user());
        DB::table('t_jenis_simpan')->where('kode_jenis_simpan', '=', $id)->delete();
         return redirect('/setting/simpanan') -> with('status', 'Data Berhasil Dihapus');
    }
}
