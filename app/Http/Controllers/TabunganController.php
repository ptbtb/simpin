<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Models\Tabungan;
use App\Models\JenisSimpanan;
use App\Models\Anggota;

class TabunganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
       
       if ($request->kode_anggota)
        {
            $data['anggota'] = Anggota::find($request->kode_anggota);
        }
        $data['title'] = "Add Saldo Awal";
        $data['listJenisSimpanan'] = $listJenisSimpanan;
        $data['request'] = $request;
        return view('tabungan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        dd($request->kode_trans[0]);
        foreach ($request->kode_trans as $key=> $kode_trans)
        {
            $id = $request->kode_anggota.str_replace('.','',$kode_trans);
            $tabungan = new Tabungan();
            $tabungan->id = $id;
            $tabungan->kode_tabungan = $request->kode_anggota;
            $tabungan->kode_anggota = $request->kode_anggota;
            $tabungan->batch = $request->batch[$key];
            $tabungan->besar_tabungan = $request->besar_tabungan[$key];
            $tabungan->deskripsi = $request->deskripsi[$key].' '.$request->batch[$key];
            $tabungan->kode_trans = $kode_trans;
            $tabungan->save();
        }
         return redirect()->route('home',['kw_kode_anggota' => $request->kode_anggota])->withSuccess("Saldo Tersimpan");
        
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}