<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;

class KodeTransaksi extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = "t_code";
    protected $primaryKey = "CODE";
    protected $keyType = 'string';

    public function jurnalItems()
    {
        return $this->hasMany(BukuBesarJurnal::class,'kode','CODE');
    }
    public function jurnalItemsDr()
    {
        return $this->hasMany(Jurnal::class,'akun_debet','CODE');
    }
    public function jurnalItemsCr()
    {
        return $this->hasMany(Jurnal::class,'akun_kredit','CODE');
    }
//    public function jurnalItemDr()
//    {
//        return $this->hasMany(Jurnal::class,'akun_debet','CODE');
//    }
//    public function jurnalItemCr()
//    {
//        return $this->hasMany(Jurnal::class,'akun_kredit','CODE');
//    }
    public function codeCategory()
    {
        return $this->belongsTo(CodeCategory::class, 'code_category_id');
    }
    public function codeType()
    {
        return $this->belongsTo(CodeType::class, 'code_type_id');
    }
    public function normalBalance()
    {
        return $this->belongsTo(NormalBalance::class, 'normal_balance_id');
    }

    public function jurnalAmount($tgl)
    {
        if(!$tgl)
        {
            $tgl = Carbon::today()->format('Y-m-d');
        }
        $y = Carbon::createFromFormat('Y-m-d', $tgl)->format('Y');
        $saldo = 0;
        $todays=Carbon::createFromFormat('Y-m-d', $tgl);
        $today=Carbon::createFromFormat('Y-m-d', $tgl)->format('Y-m-d');
        if ($this->code_type_id==3 ||$this->code_type_id==4){
            if ($y=='2020'){
                $startOf=Carbon::createFromFormat('Y-m-d', '2020-12-30')->format('Y-m-d');
            }else{
                $startOf = $todays->startOfYear()->format('Y-m-d');
            }

        }else{
            $startOf=Carbon::createFromFormat('Y-m-d', '2020-12-30')->format('Y-m-d');
        }

        $saldoDebet = $this->saldoDr($startOf,$today);
        $saldoKredit = $this->saldoCr($startOf,$today);

        $saldo = $saldoDebet-$saldoKredit;

        return $saldo;
    }
    public function neracaAmount($tgl)
    {
        if(!$tgl)
        {
            $tgl = Carbon::today()->format('Y-m-d');
        }
        $y = Carbon::createFromFormat('Y-m-d', $tgl)->format('Y');
        $saldo = 0;
        $todays=Carbon::createFromFormat('Y-m-d', $tgl);
        $today=Carbon::createFromFormat('Y-m-d', $tgl)->format('Y-m-d');
        if ($this->code_type_id==3 ||$this->code_type_id==4){
            if ($y=='2022'){
                $startOf=Carbon::parse(ActiveSaldoAwal::where('status', 1)->first()->tgl_saldo)->format('Y-m-d');
            }else{
                $startOf = $todays->startOfYear()->format('Y-m-d');
            }


        }else{
            $startOf=Carbon::parse(ActiveSaldoAwal::where('status', 1)->first()->tgl_saldo)->format('Y-m-d');
        }

        $saldoDebet = $this->saldoDr($startOf,$today);
        $saldoKredit = $this->saldoCr($startOf,$today);

        if ($this->normal_balance_id==NORMAL_BALANCE_DEBET){
            $saldo +=$saldoDebet;
            $saldo -=$saldoKredit;

        }
        if ($this->normal_balance_id==NORMAL_BALANCE_KREDIT){
            $saldo -=$saldoDebet;
            $saldo +=$saldoKredit;

        }
        if ($this->CODE=='210.00.000'){
            $saldo =-$saldo;

        }

        return $saldo;
    }


    public  function saldoDr($from,$to){
        return $this->jurnalItemsDr
            ->whereBetween('tgl_transaksi', [$from,$to])
//            ->where('trans','D')
            ->sum('debet');
    }

    public  function saldoCr($from,$to){
        return $this->jurnalItemsCr
            ->whereBetween('tgl_transaksi', [$from,$to])
//            ->where('trans','K')
            ->sum('kredit');
    }

}
