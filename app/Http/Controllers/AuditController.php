<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  OwenIt\Auditing\Models\Audit;
use Carbon\Carbon;
use Auth;

class AuditController extends Controller
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
        $this->authorize('view audit', Auth::user());
        $audit= Audit::orderby('created_at','desc')->get();
        $data['title'] = 'Audit Log';
        $data['list'] = $audit;
        return view('audit.index', $data);
    }

    
}
