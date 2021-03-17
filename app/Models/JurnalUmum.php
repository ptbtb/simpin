<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalUmum extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "t_jurnal_umum";
    protected $dates = ['tgl_transaksi'];
    protected $appends = ['view_tgl_transaksi', 'total_nominal_debet_rupiah', 'total_nominal_kredit_rupiah'];

    public function jurnalUmumItems()
    {
        return $this->hasMany(JurnalUmumItem::class);
    }

    public function jurnalUmumLampirans()
    {
        return $this->hasMany(JurnalUmumLampiran::class);
    }

    public function getViewTglTransaksiAttribute()
    {
        return $this->tgl_transaksi->format('d F Y');
    }

    public function getTotalNominalDebetRupiahAttribute()
    {
        $totalDebet = $this->jurnalUmumItems->where('code.normal_balance_id', NORMAL_BALANCE_DEBET)->sum('nominal');
        
        return number_format($totalDebet,0,",",".");
    }

    public function getTotalNominalKreditRupiahAttribute()
    {
        $totalKredit = $this->jurnalUmumItems->where('code.normal_balance_id', NORMAL_BALANCE_KREDIT)->sum('nominal');
        
        return number_format($totalKredit,0,",",".");
    }
}
