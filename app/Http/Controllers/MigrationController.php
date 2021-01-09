<?php

namespace App\Http\Controllers;

use App\Events\Pinjaman\PengajuanCreated;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Http\Request;

class MigrationController extends Controller
{
    public function index()
    {
        $operator = User::operatorSimpin()->get();
        dd($operator);
        $pengajuan = Pengajuan::orderBy('created_at','asc')->first();
        event(new PengajuanCreated($pengajuan));
        dd('a');
    }
}
