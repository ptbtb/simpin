<?php

namespace App\Http\Controllers;

use App\Models\ActiveSaldoAwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ActiveSaldoAwalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $this->authorize('view active saldo awal', $user);
        $listActive = ActiveSaldoAwal::all();
        $data['title'] = 'List Active Saldo Awal';
        $data['listActive'] = $listActive;
        return view('active_saldo_awal.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $this->authorize('add active saldo awal', $user);
        $data['title'] = 'Tambah Active Saldo Awal';
        return view('active_saldo_awal.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        try 
        {
            $user = Auth::user();
            $this->authorize('add active saldo awal', $user);
            $validator  = Validator::make($request->all(), [
                'tgl_saldo' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $dataRequest = $request->all();
            unset($dataRequest['_token']); // delete _token dari array
            DB::transaction(function () use ($dataRequest)
            {        
                ActiveSaldoAwal::create($dataRequest);
            });

            return redirect()->route('active-saldo-awal.index')->withSuccess('Data berhasil ditambahkan');
        } 
        catch (\Throwable $th) 
        {
            return redirect()->back()->withErrors($th->getMessage());    
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ActiveSaldoAwal  $activeSaldoAwal
     * @return \Illuminate\Http\Response
     */
    public function show(ActiveSaldoAwal $activeSaldoAwal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ActiveSaldoAwal  $activeSaldoAwal
     * @return \Illuminate\Http\Response
     */
    public function edit(ActiveSaldoAwal $activeSaldoAwal)
    {
        $user = Auth::user();
        $this->authorize('edit active saldo awal', $user);
        // $activeSaldoAwal = ActiveSaldoAwal::findOrFail($id);
        $data['title'] = 'Edit Active Saldo Awal';
        $data['activeSaldoAwal'] = $activeSaldoAwal;
        return view('active_saldo_awal.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ActiveSaldoAwal  $activeSaldoAwal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ActiveSaldoAwal $activeSaldoAwal)
    {
        try 
        {
            $user = Auth::user();
            $this->authorize('edit active saldo awal', $user);
            
            $validator  = Validator::make($request->all(), [
                'tgl_saldo' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $dataRequest = $request->all();
            unset($dataRequest['_token']); // delete _token dari array

            DB::transaction(function () use ($dataRequest, $activeSaldoAwal)
            {        
                $activeSaldoAwal->update($dataRequest);
            });

            return redirect()->route('active-saldo-awal.index')->withSuccess('Data berhasil disimpan');
        } 
        catch (\Throwable $th) 
        {
            return redirect()->back()->withErrors($th->getMessage());    
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ActiveSaldoAwal  $activeSaldoAwal
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActiveSaldoAwal $activeSaldoAwal)
    {
        try 
        {
            $user = Auth::user();
            $this->authorize('delete active saldo awal', $user);
            // $pengurus = Pengurus::find($id);
            if(is_null($activeSaldoAwal))
            {
                return response(['message' => 'Data tidak ditemukan'], 404);
            }
            DB::transaction(function () use ($activeSaldoAwal)
            {
                $activeSaldoAwal->delete();
            });
            return response(['message' => 'success'], 200);
        } 
        catch (\Throwable $th) 
        {
            return response(['message' => $th->getMessage()], 500);
        }
    }
}
