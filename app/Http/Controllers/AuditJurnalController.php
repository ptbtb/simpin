<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurnal;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use App\Models\AngsuranPartial;
use App\Models\Simpanan;
use App\Models\Penarikan;
use App\Models\JurnalTemp;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use App\Models\TipeJurnal;
use DB;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Log;

class AuditJurnalController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            if(!$request->from)
            {          
                $request->from = Carbon::today()->startOfDay()->format('d-m-Y');
            }
            if(!$request->to)
            {          
                $request->to = Carbon::today()->endOfDay()->format('d-m-Y');
            }

            $data['title'] = 'List Jurnal';
            $data['tipeJurnal'] = TipeJurnal::get()->pluck('name','id');
            $data['request'] = $request;
            return view('audit.jurnal', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function auditJurnalAjax(Request $request)
    {

        try
        {
           $startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->startOfDay()->format('Y-m-d');
           $endUntilPeriod = Carbon::createFromFormat   ('d-m-Y', $request->to)->endOfDay()->format('Y-m-d');
           $jurnal = Jurnal::whereDoesntHaveMorph('jurnalable', [AngsuranPartial::class,Pinjaman::class,Simpanan::class,Penarikan::class,Angsuran::class,JurnalTemp::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);


           if ($request->id_tipe_jurnal)
           {
            $jurnal = $jurnal->where('id_tipe_jurnal', $request->id_tipe_jurnal);
        }

        
    if($request->keterangan)
    {
        $jurnal = $jurnal->where('keterangan', 'like', '%' . $request->keterangan . '%');
    }
    if($request->code){
       $jurnal = $jurnal
       ->where(function ($query) use($request) {

         $query->where('akun_debet', 'like',  $request->code . '%')
         ->orwhere('akun_kredit', 'like',  $request->code . '%');

     });

   }




   $jurnal = $jurnal->orderBy('tgl_transaksi', 'desc');
   return DataTables::eloquent($jurnal)->addIndexColumn()
   ->with('totaldebet', function() use ($jurnal) {
    return $jurnal->sum('debet');
})
   ->with('totalkredit', function() use ($jurnal) {
    return $jurnal->sum('kredit');
})
   ->make(true);
}
catch (\Throwable $e)
{
    $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
    Log::error($message);
    return response()->json(['message' => 'error'], 500);
}
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function destroy(Request $request)
    {
        try {
        $user = Auth::user();
        $check = Hash::check($request->password, $user->password);
        if (!$check) {
            Log::error('Wrong Password');
            return response()->json(['message' => 'Wrong Password'], 412);
        }

            // get kode ambil's data when got from check boxes
            if (isset($request->ids)) {
            $ids = json_decode($request->ids);
            }

            foreach ($ids as $key => $id) {
                $jurnal=Jurnal::findOrFail($id);
                $jurnal->delete();
            }
            return response()->json(['message' => 'success'], 200);

        }catch (\Exception $e) {
        \Log::error($e);
        $message = $e->getMessage();
        return response()->json(['message' => $message], 500);
        }
    }
}
