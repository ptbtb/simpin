<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  OwenIt\Auditing\Models\Audit;
use Carbon\Carbon;
use Auth;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view audit', Auth::user());
        if (!$request->from) {
            $request->from = Carbon::today()->format('d-m-Y');
        }
        if (!$request->to) {
            $request->to = Carbon::today()->format('d-m-Y');
        }
        $startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->startOfDay();
        $endUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->to)->endOfDay();
        $audit = Audit::wherebetween('created_at', [$startUntilPeriod, $endUntilPeriod])->orderby('created_at', 'desc')->get();
        $data['title'] = 'Audit Log';
        $data['list'] = $audit;
        $data['request'] = $request;
        return view('audit.index', $data);
    }

    public function createExcel(Request $request)
    {
        $this->authorize('view audit', Auth::user());
        if (!$request->from) {
            $request->from = Carbon::today()->format('d-m-Y');
        }
        if (!$request->to) {
            $request->to = Carbon::today()->format('d-m-Y');
        }
        $startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->startOfDay();
        $endUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->to)->endOfDay();
        $audit = Audit::wherebetween('created_at', [$startUntilPeriod, $endUntilPeriod])->orderby('created_at', 'desc')->get();
        
    }

    public function createPdf(Request $request)
    {
    }
}
