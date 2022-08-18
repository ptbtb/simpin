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
    public function jurnalItemDr()
    {
        return $this->hasMany(Jurnal::class,'akun_debet','CODE');
    }
    public function jurnalItemCr()
    {
        return $this->hasMany(Jurnal::class,'akun_kredit','CODE');
    }
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
        $saldo = 0;
        $todays=Carbon::createFromFormat('Y-m-d', $tgl);
        $today=Carbon::createFromFormat('Y-m-d', $tgl)->format('Y-m-d');
        if ($this->code_type_id==3 ||$this->code_type_id==4){
            $startOf = $todays->startOfYear()->format('Y-m-d');

        }else{
            $startOf=Carbon::createFromFormat('Y-m-d', '2020-12-31')->format('Y-m-d');
        }


        $saldoDebet = $this->jurnalItems
            ->whereBetween('tgl_transaksi', [$startOf,$today])
            ->where('trans','D')
            ->sum('amount');
        $saldoKredit = $this->jurnalItems
            ->whereBetween('tgl_transaksi', [$startOf,$today])
            ->where('trans','K')
            ->sum('amount');
//        dd($saldoKredit);
//        if($this->normal_balance_id == NORMAL_BALANCE_DEBET){
//
//            $saldo += $saldoDebet-$saldoKredit;
//        }
//        if($this->normal_balance_id == NORMAL_BALANCE_KREDIT){
            $saldo = $saldoDebet-$saldoKredit;
//        }
//        dd($saldo);

//        if($this->code_type_id==1 && $this->code_category_id==28 ) {
//            return round(-$saldo);
//        }

//        if($this->code_type_id==2 && $this->code_type_id==4 ){
//            return round(-$saldo);
//        }


        return round($saldo);
    }
    public function jurnalAmountTransaksi($from,$to)
    {

        $saldo = 0;
        $today=Carbon::createFromFormat('Y-m-d', $to)->format('Y-m-d');
        $startOf = Carbon::createFromFormat('Y-m-d', $from)->format('Y-m-d');


        $saldoDebet = $this->jurnalItems
            ->whereBetween('tgl_transaksi', [$startOf,$today])
            ->where('trans','D')
            ->sum('amount');
        $saldoKredit = $this->jurnalItems
            ->whereBetween('tgl_transaksi', [$startOf,$today])
            ->where('trans','K')
            ->sum('amount');
//        dd($saldoKredit);
//        if($this->normal_balance_id == NORMAL_BALANCE_DEBET){
//
//            $saldo += $saldoDebet-$saldoKredit;
//        }
//        if($this->normal_balance_id == NORMAL_BALANCE_KREDIT){
        $saldo = $saldoDebet-$saldoKredit;
//        }
//        dd($saldo);

//        if($this->code_type_id==1 && $this->code_category_id==28 ) {
//            return round(-$saldo);
//        }

//        if($this->code_type_id==2 && $this->code_type_id==4 ){
//            return round(-$saldo);
//        }


        return round($saldo);
    }
}
