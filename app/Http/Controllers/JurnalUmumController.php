<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Code;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumItem;
use App\Models\Jurnal;
use App\Models\jurnalUmumLampiran;
use Illuminate\Http\Request;
use App\Managers\JurnalManager;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; 

use Auth;
use DB;
use Hash;
use Carbon\Carbon;
use Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class JurnalUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view jurnal umum', Auth::user());
        $listJurnalUmum = JurnalUmum::with('jurnalUmumItems', 'jurnalUmumLampirans');
        $listJurnalUmum = $listJurnalUmum->orderBy('created_at','desc');

        $data['title'] = "List Jurnal Umum";
        $data['request'] = $request;
        $data['listJurnalUmum'] = $listJurnalUmum;

        return view('jurnal_umum.index', $data);
    }

    public function indexAjax(Request $request)
    {
        $this->authorize('view jurnal umum', Auth::user());
        $listJurnalUmum = JurnalUmum::with('jurnalUmumItems', 'jurnalUmumLampirans');
        $listJurnalUmum = $listJurnalUmum->orderBy('created_at','desc');
        return DataTables::eloquent($listJurnalUmum)->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('add jurnal umum', Auth::user());
        $debetCodes = Code::where('is_parent', 0)
                            ->where('CODE', 'not like', "411%")
                            ->where('CODE', 'not like', "106%")
                            ->where('CODE', 'not like', "502%")
                            ->where('CODE', 'not like', "105%")
                            ->where('normal_balance_id', NORMAL_BALANCE_DEBET)
                            ->get();

        $creditCodes = Code::where('is_parent', 0)
                            ->where('CODE', 'not like', "411%")
                            ->where('CODE', 'not like', "106%")
                            ->where('CODE', 'not like', "502%")
                            ->where('CODE', 'not like', "105%")
                            ->where('normal_balance_id', NORMAL_BALANCE_KREDIT)
                            ->get();
        
        $data['title'] = "Tambah Jurnal Umum";
        $data['request'] = $request;
        $data['debetCodes'] = $debetCodes;
        $data['creditCodes'] = $creditCodes;
        return view('jurnal_umum.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('add jurnal umum', Auth::user());
        try
        {
            // check password
            $check = Hash::check($request->password, Auth::user()->password);
            if (!$check)
            {
                return redirect()->back()->withError("Password yang anda masukkan salah");
            }
            
            // get auth user
            $user = Auth::user();

            // save into jurnal umum
            $jurnalUmum = new JurnalUmum();
            $jurnalUmum->tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
            $jurnalUmum->deskripsi = $request->deskripsi;
            $jurnalUmum->save();
            
            // loop every item
            for ($i=0; $i < count($request->code_id) ; $i++) 
            { 
                $nominal = filter_var($request->nominal[$i], FILTER_SANITIZE_NUMBER_INT);

                $jurnalUmumItem = new JurnalUmumItem();
                $jurnalUmumItem->jurnal_umum_id = $jurnalUmum->id;
                $jurnalUmumItem->code_id = $request->code_id[$i];
                $jurnalUmumItem->nominal = $nominal;
                $jurnalUmumItem->save();
            }

            // loop every lampiran
            for ($i=0; $i < count($request->lampiran) ; $i++) 
            { 
                $jurnalUmumLampiran = new JurnalUmumLampiran();
                $jurnalUmumLampiran->jurnal_umum_id = $jurnalUmum->id;

                // check file lampiran
                $file = $request->lampiran[$i];
                if ($file) 
                {
                    $config['disk'] = 'upload';
                    $config['upload_path'] = '/jurnalumum/' . $jurnalUmum->id;
                    $config['public_path'] = env('APP_URL') . '/upload/jurnalumum/' . $jurnalUmum->id;

                    // create directory if doesn't exist
                    if (!Storage::disk($config['disk'])->has($config['upload_path'])) {
                        Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
                    }

                    // upload file if valid
                    if ($file->isValid()) {
                        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                        Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file, $filename);
                        $jurnalUmumLampiran->lampiran = $config['disk'] . $config['upload_path'] . '/' . $filename;
                    }
                }
                
                $jurnalUmumLampiran->save();
            }
            
            // call function for create Jurnal
            if($jurnalUmum)
            {
                JurnalManager::createJurnalUmum($jurnalUmum);
            }

            return redirect()->route('jurnal-umum-list')->withSuccess('Berhasil menambah transaksi');
        }
        catch (\Throwable $th)
        {
            \Log::error($th);
            return redirect()->back()->withError('Gagal menyimpan data');
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
        $this->authorize('edit jurnal umum', Auth::user());
        $jurnalUmum = JurnalUmum::with('jurnalUmumItems', 'jurnalUmumLampirans')->find($id);
        $debetCodes = Code::where('is_parent', 0)
                ->where('CODE', 'not like', "411%")
                ->where('CODE', 'not like', "106%")
                ->where('CODE', 'not like', "502%")
                ->where('CODE', 'not like', "105%")
                ->where('normal_balance_id', NORMAL_BALANCE_DEBET)
                ->get();

        $creditCodes = Code::where('is_parent', 0)
                ->where('CODE', 'not like', "411%")
                ->where('CODE', 'not like', "106%")
                ->where('CODE', 'not like', "502%")
                ->where('CODE', 'not like', "105%")
                ->where('normal_balance_id', NORMAL_BALANCE_KREDIT)
                ->get();

        $itemDebets = $jurnalUmum->jurnalUmumItems->where('code.normal_balance_id', NORMAL_BALANCE_DEBET);
        $itemCredits = $jurnalUmum->jurnalUmumItems->where('code.normal_balance_id', NORMAL_BALANCE_KREDIT);

        $data['title'] = "Edit Jurnal Umum";
        $data['jurnalUmum'] = $jurnalUmum;
        $data['debetCodes'] = $debetCodes;
        $data['creditCodes'] = $creditCodes;
        $data['itemDebets'] = $itemDebets;
        $data['itemCredits'] = $itemCredits;
        return view('jurnal_umum.edit', $data);
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
        $this->authorize('edit jurnal umum', Auth::user());
        try
        {
            // check password
            $check = Hash::check($request->password, Auth::user()->password);
            if (!$check)
            {
                return redirect()->back()->withError("Password yang anda masukkan salah");
            }

            // get auth user
            $user = Auth::user();

            // check into jurnal umum
            $jurnalUmum = JurnalUmum::find($id);
            $jurnalUmum->tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
            $jurnalUmum->deskripsi = $request->deskripsi;
            $jurnalUmum->save();

            $jurnalUmumItems = $jurnalUmum->jurnalUmumItems;
            
            // loop every item
            for ($i=0; $i < count($request->code_id) ; $i++) 
            { 
                // update item
                if ($i < $jurnalUmumItems->count())
                {
                    $jurnalUmumItem = $jurnalUmumItems[$i];
                }
                else
                {
                    $jurnalUmumItem = new JurnalUmumItem();
                }
                
                $nominal = filter_var($request->nominal[$i], FILTER_SANITIZE_NUMBER_INT);

                $jurnalUmumItem->jurnal_umum_id = $jurnalUmum->id;
                $jurnalUmumItem->code_id = $request->code_id[$i];
                $jurnalUmumItem->nominal = $nominal;
                $jurnalUmumItem->save();
            }

            // delete item if jurnal umum items less than request jurnal umum items
            if (count($request->code_id) < $jurnalUmumItems->count())
            {
                for ($i=count($request->code_id); $i < $jurnalUmumItems->count(); $i++) { 
                    $jurnalUmumItem = $jurnalUmumItems[$i];
                    $jurnalUmumItem->delete();
                }
            }

            $jurnalUmumLampirans = $jurnalUmum->jurnalUmumLampirans;
            if($request->lampiran)
            {
                // loop every lampiran
                for ($i=0; $i < count($request->lampiranCounts) ; $i++) 
                { 
                    // check if lampiran is exist
                    if(isset($request->lampiran[$i]))
                    {
                        // update item
                        if ( ($i + 1) < $jurnalUmumLampirans->count())
                        {
                            $jurnalUmumLampiran = $jurnalUmumLampirans[$i];
                        }
                        else
                        {
                            $jurnalUmumLampiran = new JurnalUmumLampiran();
                            $jurnalUmumLampiran->jurnal_umum_id = $jurnalUmum->id;
                        }

                        // check file lampiran
                        $file = $request->lampiran[$i];
                        if ($file) 
                        {
                            $config['disk'] = 'upload';

                            // delete if existing file is replaced
                            if ( ($i + 1) < $jurnalUmumLampirans->count())
                            {
                                // delete old file
                                File::delete($jurnalUmumLampirans[$i]->lampiran);
                            }
                            
                            $config['upload_path'] = '/jurnalumum/' . $jurnalUmum->id;
                            $config['public_path'] = env('APP_URL') . '/upload/jurnalumum/' . $jurnalUmum->id;

                            // create directory if doesn't exist
                            if (!Storage::disk($config['disk'])->has($config['upload_path'])) {
                                Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
                            }

                            // upload file if valid
                            if ($file->isValid()) {
                                $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                                Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file, $filename);
                                $jurnalUmumLampiran->lampiran = $config['disk'] . $config['upload_path'] . '/' . $filename;
                            }
                        }

                        $jurnalUmumLampiran->save();
                    }
                    
                }
            }

            // delete item if jurnal umum lampirans less than request jurnal umum lampirans
            if (count($request->lampiranCounts) < $jurnalUmumLampirans->count())
            {
                for ($i=count($request->lampiranCounts); $i < $jurnalUmumLampirans->count(); $i++) { 
                    $jurnalUmumLampiran = $jurnalUmumLampirans[$i];

                    // delete old file
                    File::delete($jurnalUmumLampiran->lampiran);
                    
                    $jurnalUmumLampiran->delete();
                }
            }

            // update jurnal
            if($jurnalUmum)
            {
                // update jurnal data
                // JurnalManager::updateJurnalUmum($jurnalUmum);
            }
            
            return redirect()->route('jurnal-umum-list')->withSuccess('Berhasil merubah transaksi');
        }
        catch (\Throwable $th)
        {
            dd($th);
            \Log::error($th);
            return redirect()->back()->withError('Gagal menyimpan data');
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
        //
    }
}
