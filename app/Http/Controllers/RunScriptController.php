<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RunScriptController extends Controller
{
    public function index()
    {
        $this->authorize('view audit', Auth::user());
        $data['title'] = 'Run Script';
        return view('runscript.index', $data);
    }

    public function runScript(Request $request)
    {
        try {
            if ($request->script == COMMAND_JURNAL_BALANCE_RESOLVER) {
                Artisan::call('console:jurnal', ['--periode' => $request->periode]);
            }
            elseif ($request->script == COMMAND_JURNAL_PENARIKAN_GENERATOR) {
                Artisan::call('update:jurnalpenarikan', ['--periode' => $request->periode]);
            }
            elseif ($request->script == COMMAND_JURNAL_UMUM_GENERATOR) {
                Artisan::call('update:jurnalju', ['--periode' => $request->periode]);
            }
            return redirect()->back()->withSuccess('Done running script, open log to see details..');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->back()->withErrors('Error');
        }
    }
}
