<?php

namespace App\Http\Controllers;

use App\Models\Pengurus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PengurusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listPengurus = Pengurus::orderBy('expired', 'desc')
                                ->get();
        $data['title'] = 'List Pengurus';
        $data['listPengurus'] = $listPengurus;
        return view('pengurus.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Tambah Pengurus';
        return view('pengurus.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try 
        {
            $validator  = Validator::make($request->all(), [
                'nama' => 'required',
                'jabatan' => 'required',
                'start' => 'required',
                'expired' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $dataRequest = $request->all();
            // hilangkan _token dari array
            unset($dataRequest['_token']);

            // ambil data pengurus dengan jabatan yang sama dan belum expired
            $pengurusSekarang = Pengurus::where('jabatan', $request->jabatan)
                                        ->whereDate('expired', '>=', Carbon::now()->toDateString())
                                        ->first();

            // jika masih ada pengurus dengan jabatan sama dan belum expired, maka tolak 
            if($pengurusSekarang)
            {
                return redirect()->back()->withErrors('Gagal input data. Masih ada pengurus dengan jabatan sama yang masih aktif');
            }

            // jika tidak ada, maka tambah pengurus
            DB::transaction(function () use ($dataRequest)
            {        
                Pengurus::create($dataRequest);
            });

            return redirect()->route('pengurus.index')->withSuccess('Data berhasil ditambahkan');
        } 
        catch (\Throwable $th) 
        {
            return redirect()->back()->withErrors($th->getMessage());    
        }
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
        $pengurus = Pengurus::findOrFail($id);
        $data['title'] = 'Edit Pengurus';
        $data['pengurus'] = $pengurus;
        return view('pengurus.edit', $data);
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
        try 
        {
            $pengurus = Pengurus::findOrFail($id);
            
            $validator  = Validator::make($request->all(), [
                'nama' => 'required',
                'jabatan' => 'required',
                'start' => 'required',
                'expired' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $dataRequest = $request->all();
            // hilangkan _token dari array
            unset($dataRequest['_token']);

            // ambil data pengurus dengan jabatan yang sama dan belum expired
            $pengurusSekarang = Pengurus::where('jabatan', $request->jabatan)
                                        ->where('id', '!=', $pengurus->id)
                                        ->whereDate('expired', '>=', Carbon::now()->toDateString())
                                        ->first();

            // jika masih ada pengurus dengan jabatan sama dan belum expired, maka tolak 
            if($pengurusSekarang)
            {
                return redirect()->back()->withErrors('Gagal input data. Masih ada pengurus dengan jabatan sama yang masih aktif');
            }

            // jika tidak ada, maka tambah pengurus
            DB::transaction(function () use ($dataRequest, $pengurus)
            {        
                $pengurus->update($dataRequest);
            });

            return redirect()->route('pengurus.index')->withSuccess('Data berhasil disimpan');
        } 
        catch (\Throwable $th) 
        {
            return redirect()->back()->withErrors($th->getMessage());    
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
        /**
         * jika di akses dengan ajax, maka harus di return dengan format return response (array data nya, status)
         * status 200 = success
         * status dengan awalan 4 atau 5, berarti error
         */
        try 
        {
            $pengurus = Pengurus::find($id);
            if(is_null($pengurus))
            {
                return response(['message' => 'Pengurus tidak ditemukan'], 404);
            }
            DB::transaction(function () use ($pengurus)
            {
                $pengurus->delete();
            });
            return response(['message' => 'success'], 200);
        } 
        catch (\Throwable $th) 
        {
            return response(['message' => $th->getMessage()], 500);
        }
    }
}
