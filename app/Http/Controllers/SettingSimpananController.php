<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\JenisSimpanan;

use Auth;
use Carbon\Carbon;

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

        $data['title'] = 'Create Jenis Simpanan';
        return view('setting.simpanan.create', $data);
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
        try {
            $jenisSimpanan = new JenisSimpanan();
            $jenisSimpanan->kode_jenis_simpan = $request->kode_jenis_simpan;
            $jenisSimpanan->nama_simpanan = $request->nama_simpanan;
            $jenisSimpanan->besar_simpanan = filter_var($request->besar_simpanan, FILTER_SANITIZE_NUMBER_INT);
            $jenisSimpanan->tgl_tagih = $request->tgl_tagih;
            $jenisSimpanan->hari_jatuh_tempo = $request->hari_jatuh_tempo;
            $jenisSimpanan->u_entry = $request->u_entry;
            $jenisSimpanan->tgl_entri = Carbon::now();
            // dd($request);
            $jenisSimpanan->save();
            
            return redirect()->route('jenis-simpanan-list')->withSuccess('Create Jenis Simpanan Success');
            
        } catch (\Exception $e) {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
                $message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
        }
        
        // DB::table('t_jenis_simpan') -> insert([
        //     'kode_jenis_simpan' => $request -> kode_jenis_simpan,
        //     'nama_simpanan' => $request -> nama_simpanan,
        //     'besar_simpanan' => filter_var($request->besar_simpanan, FILTER_SANITIZE_NUMBER_INT),
        //     'tgl_tagih' => $request -> tgl_tagih,
        //     'hari_jatuh_tempo' => $request -> hari_jatuh_tempo,
        //     'u_entry' => $request -> u_entry,
        //     'tgl_entri' => Carbon::now(),
            
        // ]);
        
        // $this -> validate($request, [
        //     'kode_jenis_simpan' => 'required',
        //     'nama_simpanan' => 'required',
        //     'besar_simpanan' => 'required',
        //     'tgl_tagih' => 'required',
        //     'hari_jatuh_tempo' => 'required',
        //     'u_entry' => 'required',
        //     'tgl_entri' => 'required',
            
        // ]);

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
        $data['simpanan'] = $simpanan;
        $data['title'] = 'Edit Jenis Simpanan';
        return view('setting.simpanan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $this->authorize('edit jenis simpanan', Auth::user());
        $jenisSimpanan = JenisSimpanan::find($request->kode_jenis_simpan);
        if (is_null($jenisSimpanan))
    	{
    		return redirect()->back()->withError('Jenis Simpanan not found');
		}
        try {
            $jenisSimpanan->nama_simpanan = $request->nama_simpanan;
            $jenisSimpanan->besar_simpanan = filter_var($request->besar_simpanan, FILTER_SANITIZE_NUMBER_INT);
            $jenisSimpanan->tgl_tagih = $request->tgl_tagih;
            $jenisSimpanan->hari_jatuh_tempo = $request->hari_jatuh_tempo;
            $jenisSimpanan->u_entry = $request->u_entry;
            $jenisSimpanan->tgl_entri = Carbon::now();
            $jenisSimpanan->save();
            
            return redirect()->route('jenis-simpanan-list')->withSuccess('Update Jenis Simpanan Success');
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
    public function destroy($id)
    {
        $this->authorize('delete jenis simpanan', Auth::user());

        $jenisSimpanan = JenisSimpanan::findOrFail($id);
        $jenisSimpanan->delete();
        return redirect()->route('jenis-simpanan-list')->withSuccess('Delete Jenis Simpanan Success');
    }
}
