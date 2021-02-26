<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\TipeJurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class JurnalController extends Controller
{
    public function index(Request $reqeust)
    {
        $this->authorize('view jurnal', Auth::user());
        try
        {
            $data['title'] = 'List Jurnal';
            $data['tipeJurnal'] = TipeJurnal::get()->pluck('name','id');
            $data['request'] = $reqeust;
            return view('jurnal.index', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function indexAjax(Request $request)
    {
        try
        {
            $jurnal = Jurnal::with('tipeJurnal','createdBy');
            if ($request->id_tipe_jurnal)
            {
                $jurnal = $jurnal->where('id_tipe_jurnal', $request->id_tipe_jurnal);
            }
            $jurnal = $jurnal->orderBy('created_at', 'desc');
            return DataTables::eloquent($jurnal)->make(true);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            return response()->json(['message' => 'error'], 500);
        }
    }
}
