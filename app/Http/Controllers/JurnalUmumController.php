<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Code;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumItem;
use App\Models\Jurnal;
use App\Models\JurnalUmumLampiran;
use Illuminate\Http\Request;
use App\Managers\JurnalManager;
use App\Managers\JurnalUmumManager;

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
        $listJurnalUmum = JurnalUmum::with('jurnalUmumItems', 'jurnalUmumLampirans','createdBy','updatedBy', 'statusJurnalUmum');
        $listJurnalUmum = $listJurnalUmum->orderBy('created_at','desc');

        $data['title'] = "List Jurnal Umum";
        $data['request'] = $request;
        $data['listJurnalUmum'] = $listJurnalUmum;

        return view('jurnal_umum.index', $data);
    }

    public function indexAjax(Request $request)
    {
        $this->authorize('view jurnal umum', Auth::user());
        $listJurnalUmum = JurnalUmum::with('jurnalUmumItems', 'jurnalUmumLampirans','createdBy','updatedBy', 'statusJurnalUmum');
        $listJurnalUmum = $listJurnalUmum->orderBy('created_at','desc');
        return DataTables::eloquent($listJurnalUmum)->addIndexColumn()->make(true);
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
                            ->get();

        $creditCodes = Code::where('is_parent', 0)
                            ->where('CODE', 'not like', "411%")
                            ->where('CODE', 'not like', "106%")
                            ->where('CODE', 'not like', "502%")
                            ->where('CODE', 'not like', "105%")
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

            // get next serial number
            $nextSerialNumber = JurnalUmumManager::getSerialNumber($request->tgl_transaksi);

            // save into jurnal umum
            $jurnalUmum = new JurnalUmum();
            $jurnalUmum->tgl_transaksi = Carbon::createFromFormat('Y-m-d', $request->tgl_transaksi);
            $jurnalUmum->deskripsi = $request->deskripsi;
            $jurnalUmum->serial_number = $nextSerialNumber;
            $jurnalUmum->save();
            
            // loop every item
            // total
            $totalDebet = 0;
            $totalCredit = 0;

            // debet
            for ($i=0; $i < count($request->code_debet_id) ; $i++) 
            { 
                $filterNominal = filter_var($request->nominal[$i], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $nominal = str_replace(",", ".", $filterNominal);
                $jurnalUmumItem = new JurnalUmumItem();
                $jurnalUmumItem->jurnal_umum_id = $jurnalUmum->id;
                $jurnalUmumItem->code_id = $request->code_debet_id[$i];
                $jurnalUmumItem->nominal = $nominal;
                $jurnalUmumItem->normal_balance_id = NORMAL_BALANCE_DEBET;
                $jurnalUmumItem->save();

                $totalDebet += $nominal;
            }
            // credit
            for ($i=0; $i < count($request->code_credit_id) ; $i++) 
            { 
                $filterNominal = filter_var($request->nominal[$i + count($request->code_debet_id)], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $nominal = str_replace(",", ".", $filterNominal);

                $jurnalUmumItem = new JurnalUmumItem();
                $jurnalUmumItem->jurnal_umum_id = $jurnalUmum->id;
                $jurnalUmumItem->code_id = $request->code_credit_id[$i];
                $jurnalUmumItem->nominal = $nominal;
                $jurnalUmumItem->normal_balance_id = NORMAL_BALANCE_KREDIT;
                $jurnalUmumItem->save();

                $totalCredit += $nominal;
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
            
            if( $totalDebet > 10000000 || $totalCredit > 10000000)
            {
                // add status
                $jurnalUmum->status_jurnal_umum_id = STATUS_JURNAL_UMUM_MENUNGGU_KONFIRMASI;
                $jurnalUmum->save();
            }
            else
            {
                // add status
                $jurnalUmum->status_jurnal_umum_id = STATUS_JURNAL_UMUM_DITERIMA;
                $jurnalUmum->save();

                // call function for create Jurnal
                if($jurnalUmum)
                {
                    JurnalManager::createJurnalUmum($jurnalUmum);
                }
            }

            return redirect()->route('jurnal-umum-list')->withSuccess('Berhasil menambah transaksi');
        }
        catch (\Throwable $th)
        {
            dd($th);
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
        $user = Auth::user();
        $this->authorize('view jurnal umum', $user);

        $jurnalUmum = JurnalUmum::with('jurnalUmumItems', 'jurnalUmumLampirans','createdBy','updatedBy')
                        ->find($id);

        $itemDebets = $jurnalUmum->jurnalUmumItems->where('normal_balance_id', NORMAL_BALANCE_DEBET);
        $itemCredits = $jurnalUmum->jurnalUmumItems->where('normal_balance_id', NORMAL_BALANCE_KREDIT);

        $data['jurnalUmum'] = $jurnalUmum;
        $data['itemDebets'] = $itemDebets;
        $data['itemCredits'] = $itemCredits;
        $data['title'] = 'Detail Jurnal Umum';

        return view('jurnal_umum.detail', $data);
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
        $jurnalUmum = JurnalUmum::with('jurnalUmumItems', 'jurnalUmumLampirans','createdBy','updatedBy')->find($id);
        $debetCodes = Code::where('is_parent', 0)
                        ->where('CODE', 'not like', "411%")
                        ->where('CODE', 'not like', "106%")
                        ->where('CODE', 'not like', "502%")
                        ->where('CODE', 'not like', "105%")
                        ->get();

        $creditCodes = Code::where('is_parent', 0)
                        ->where('CODE', 'not like', "411%")
                        ->where('CODE', 'not like', "106%")
                        ->where('CODE', 'not like', "502%")
                        ->where('CODE', 'not like', "105%")
                        ->get();

        $itemDebets = $jurnalUmum->jurnalUmumItems->where('normal_balance_id', NORMAL_BALANCE_DEBET);
        $itemCredits = $jurnalUmum->jurnalUmumItems->where('normal_balance_id', NORMAL_BALANCE_KREDIT);

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

            $jurnalUmumItemDebets = $jurnalUmum->jurnalUmumItems->where('normal_balance_id', NORMAL_BALANCE_DEBET);
            $jurnalUmumItemKredits = $jurnalUmum->jurnalUmumItems->where('normal_balance_id', NORMAL_BALANCE_KREDIT);

            $jurnalUmumItemDebets = $jurnalUmumItemDebets->values();
            $jurnalUmumItemKredits = $jurnalUmumItemKredits->values();
            
            // loop every item
            // debet
            for ($i=0; $i < count($request->code_debet_id) ; $i++) 
            { 
                // update item
                if ($i < $jurnalUmumItemDebets->count())
                {
                    $jurnalUmumItem = $jurnalUmumItemDebets[$i];
                }
                else
                {
                    $jurnalUmumItem = new JurnalUmumItem();
                }
                
                $filterNominal = filter_var($request->nominal_debet[$i],  FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $nominal = str_replace(",", ".", $filterNominal);

                $jurnalUmumItem->jurnal_umum_id = $jurnalUmum->id;
                $jurnalUmumItem->code_id = $request->code_debet_id[$i];
                $jurnalUmumItem->nominal = $nominal;
                $jurnalUmumItem->normal_balance_id = NORMAL_BALANCE_DEBET;
                $jurnalUmumItem->save();
            }
            
            // kredit
            for ($i=0; $i < count($request->code_credit_id) ; $i++) 
            { 
                // update item
                if ($i < $jurnalUmumItemKredits->count() )
                {
                    $jurnalUmumItem = $jurnalUmumItemKredits[$i];
                }
                else
                {
                    $jurnalUmumItem = new JurnalUmumItem();
                }
                
                $filterNominal = filter_var($request->nominal_credit[$i], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
                $nominal = str_replace(",", ".", $filterNominal);

                $jurnalUmumItem->jurnal_umum_id = $jurnalUmum->id;
                $jurnalUmumItem->code_id = $request->code_credit_id[$i];
                $jurnalUmumItem->nominal = $nominal;
                $jurnalUmumItem->normal_balance_id = NORMAL_BALANCE_KREDIT;
                $jurnalUmumItem->save();
            }

            // delete item if jurnal umum items less than request jurnal umum items
            // debet
            if (count($request->code_debet_id) < $jurnalUmumItemDebets->count())
            {
                for ($i=count($request->code_debet_id); $i < $jurnalUmumItemDebets->count(); $i++) { 
                    $jurnalUmumItem = $jurnalUmumItemDebets[$i];
                    $jurnalUmumItem->delete();
                }
            }
            // credit
            if (count($request->code_credit_id) < $jurnalUmumItemKredits->count() )
            {
                for ($i= count($request->code_credit_id); $i < $jurnalUmumItemKredits->count(); $i++) { 
                    $jurnalUmumItem = $jurnalUmumItemKredits[$i];
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
                JurnalManager::updateJurnalUmum($jurnalUmum);
            }
            
            return redirect()->route('jurnal-umum-list')->withSuccess('Berhasil merubah transaksi');
        }
        catch (\Throwable $th)
        {
            \Log::error($th);
            return redirect()->back()->withError('Gagal menyimpan data');
        }
    }

    public function updateStatusJurnalUmum(Request $request) {
        try {
            $user = Auth::user();
            $check = Hash::check($request->password, $user->password);
            if (!$check) {
                Log::error('Wrong Password');
                return response()->json(['message' => 'Wrong Password'], 412);
            }

            // get kode ambil's data when got from check boxes
            if (isset($request->ids))
            {
                $ids = json_decode($request->ids);
            }

            foreach ($ids as $key => $id)
            {
                $jurnalUmum = JurnalUmum::where('id', $id)->first();

                // check jurnalUmum's status must same as old_status
                if($jurnalUmum && $jurnalUmum->status_jurnal_umum_id == $request->old_status)
                {

                    if ($request->status == STATUS_JURNAL_UMUM_DIBATALKAN) 
                    {
                        $jurnalUmum->status_jurnal_umum_id = STATUS_JURNAL_UMUM_DIBATALKAN;
                        $jurnalUmum->save();
                        return response()->json(['message' => 'success'], 200);
                    }

                    $this->authorize('approve pengajuan pinjaman', $user);
                    if (is_null($jurnalUmum)) 
                    {
                        return response()->json(['message' => 'not found'], 404);
                    }

                    $jurnalUmum->status_jurnal_umum_id = $request->status;
                    $jurnalUmum->tgl_acc = Carbon::now();
                    $jurnalUmum->approved_by = $user->id;
                    $jurnalUmum->save();

                    if ($request->status == STATUS_JURNAL_UMUM_DITERIMA) 
                    {
                        JurnalManager::createJurnalUmum($jurnalUmum);
                    }
                }
            }

            return response()->json(['message' => 'success'], 200);
        } catch (\Exception $e) {
            \Log::error($e);
            $message = $e->getMessage();
            return response()->json(['message' => $message], 500);
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

    public function indexJkk()
    {
        try
        {
            $listJurnalUmum = JurnalUmum::needPrintJkk()
                                        ->get();

            $data['title'] = "Print JKK";
            $data['listJurnalUmum'] = $listJurnalUmum;
            return view('jurnal_umum.indexPrintJKK',$data);
        }
        catch (\Throwable $e)
        {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function printJkk(Request $request)
    {
        try
        {
            $jurnalUmum = JurnalUmum::where('id', $request->kode_jurnal_umum)
                                        ->first();

            $jurnalUmum->status_jkk = 1;
            $jurnalUmum->save();
            
            
            $data['jurnalUmum'] = $jurnalUmum;
            $data['tgl_print']= Carbon::now()->format('d-m-Y');
            $data['terbilang'] = self::terbilang($jurnalUmum->total_nominal_debet) . ' rupiah';

            view()->share('data',$data);
            PDF::setOptions(['margin-left' => 0,'margin-right' => 0]);
            $pdf = PDF::loadView('jurnal_umum.printJKK', $data)->setPaper('a4', 'portrait');

            // download PDF file with download method
            $filename = $jurnalUmum->serial_number_view.'-'.$data['tgl_print'].'.pdf';
            return $pdf->download($filename);

            return view('jurnal_umum.printJKK', $data);
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    static function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = self::penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = self::penyebut($nilai / 10) . " puluh" . self::penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . self::penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = self::penyebut($nilai / 100) . " ratus" . self::penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . self::penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = self::penyebut($nilai / 1000) . " ribu" . self::penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = self::penyebut($nilai / 1000000) . " juta" . self::penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = self::penyebut($nilai / 1000000000) . " milyar" . self::penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = self::penyebut($nilai / 1000000000000) . " trilyun" . self::penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    static function terbilang($nilai) {
        if ($nilai < 0) {
            $hasil = "minus " . trim(self::penyebut($nilai));
        } else {
            $hasil = trim(self::penyebut($nilai));
        }
        return $hasil;
    }
}
