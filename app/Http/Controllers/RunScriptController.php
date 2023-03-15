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
            $return = 2;
            $transaksi = '';
            if ($request->script == COMMAND_JURNAL_BALANCE_RESOLVER) {
                $transaksi = 'resolver';
                Artisan::call('console:jurnal', ['--periode' => $request->periode]);
            }
            elseif ($request->script == COMMAND_JURNAL_PENARIKAN_GENERATOR) {
                $transaksi = 'penarikan';
                $return = Artisan::call('update:jurnalpenarikan', ['--periode' => $request->periode]);
            }
            elseif ($request->script == COMMAND_JURNAL_UMUM_GENERATOR) {
                $transaksi = 'jurnal umum';
                $return = Artisan::call('update:jurnalju', ['--periode' => $request->periode]);
            }
            elseif ($request->script == COMMAND_JURNAL_PINJAMAN_GENERATOR) {
                $transaksi = 'pinjaman';
                $return = Artisan::call('update:jurnalpinjaman', ['--periode' => $request->periode]);
            }
            elseif ($request->script == COMMAND_JURNAL_SIMPANAN_GENERATOR) {
                $transaksi = 'simpanan';
                $return = Artisan::call('update:jurnalsimpanan', ['--periode' => $request->periode]);
            }
            elseif ($request->script == COMMAND_JURNAL_ANGSURAN_GENERATOR) {
                $transaksi = 'angsuran';
                $return = Artisan::call('update:jurnalangsuran', ['--periode' => $request->periode]);
            }

            if ($transaksi == 'resolver'){
                return redirect()->back()->withSuccess('Done running script, open log to see details..');
            }
            if ($return == 0){
                return redirect()->back()->withSuccess('Done running script, open log to see details..');
            }
            elseif ($return == 1){
                return redirect()->back()->withSuccess('Done running script, but there is no ' . $transaksi . ' without journal');
            }
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->back()->withErrors('Error');
        }
    }
}
