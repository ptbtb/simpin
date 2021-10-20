<?php

namespace App\Http\Controllers;
use App\Managers\PinjamanManager;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\Auth;

class PinjamanStatusController extends Controller
{
    //
    public static function runup()
    {
        $listPinjaman = Pinjaman::where('saldo_mutasi','>',0)->get();
        // dd($listPinjaman->count());
        $a=0;
        $all=$listPinjaman->count();
        foreach ($listPinjaman as $pinjaman)
        {
            
             $angsuran = Angsuran::where('kode_pinjam',$pinjaman->kode_pinjam)
                ->where('id_status_angsuran',2)->get();
                $angs =Angsuran::where('kode_pinjam',$pinjaman->kode_pinjam)->get();
                $jangs =$angs->count();
                $sumangsuran = $angsuran->sum('besar_angsuran');
                $countangsuran = $angsuran->count();
               
                $pinjaman->sisa_pinjaman = $pinjaman->saldo_mutasi - $sumangsuran;
                $pinjaman->sisa_angsuran = $jangs - $countangsuran;
                if ($pinjaman->sisa_pinjaman>0){
                    $pinjaman->id_status_pinjaman = 1;
                }else{
                    $pinjaman->id_status_pinjaman = 2;
                }
                $pinjaman->save();
                foreach ($angs as $ags){
                    $ags->sisa_pinjam = $pinjaman->sisa_pinjaman;
                    $ags->save();
                }

           $a++;  
        printf($a." of ".$all."\r\n");
        }
    }

    public static function runangs()
    {
        $angs = Angsuran::where('id_status_angsuran',1)
        ->where('besar_pembayaran','>',0)
        ->get();
        $a=0;
        $all=$angs->count();

        foreach ($angs as $ags){
            $pinjaman = Pinjaman::where('kode_pinjam',$ags->kode_pinjam)->first();
            if ($ags->besar_pembayaran >= $ags->totalAngsuran){
                $ags->id_status_angsuran = STATUS_ANGSURAN_LUNAS;
                $pinjaman->sisa_angsuran = $pinjaman->sisa_angsuran - 1;
                $pinjaman->save();
            }
            $ags->save();
        $a++;  
        printf($a." of ".$all."\r\n");          
        } 
        
    }
}
