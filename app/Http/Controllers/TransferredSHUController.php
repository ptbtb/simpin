<?php

namespace App\Http\Controllers;

use App\Imports\TransferredSHUImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransferredSHUController extends Controller
{
    public function index()
    {
        $this->authorize('import user', Auth::user());
        $query = "SELECT a.kode_anggota, b.nama_anggota, a.amount FROM shu_transferred a INNER JOIN t_anggota b ON a.kode_anggota = b.kode_anggota
        ";
        $items = DB::select($query);
        $data['title'] = 'List SHU Ditransfer';
        $data['items'] = $items;
        return view('shu.index', $data);
    }
    public function import()
    {
        $this->authorize('import user', Auth::user());
        $data['title'] = 'Import SHU Ditransfer';
        return view('shu.import', $data);
    }

    public function storeImport(Request $request)
    {
        $this->authorize('import user', Auth::user());
        try
        {
            Excel::import(new TransferredSHUImport, $request->file);
            return redirect()->back()->withSuccess('Import data berhasil');
        }
        catch (\Throwable $e)
        {
            return redirect()->back()->withError('Gagal import data');
        }
    }
}
