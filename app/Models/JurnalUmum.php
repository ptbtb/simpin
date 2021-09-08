<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class JurnalUmum extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Userstamps;

    protected $table = "t_jurnal_umum";
    protected $dates = ['tgl_transaksi'];
    protected $appends = ['view_tgl_transaksi', 'total_nominal_debet_rupiah', 'total_nominal_kredit_rupiah', 'serial_number_view'];

    public function jurnalUmumItems()
    {
        return $this->hasMany(JurnalUmumItem::class);
    }
    public function createdBy()
    {
        return $this->belongsto(User::class,'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsto(User::class,'updated_by');
    }

    public function jurnalUmumLampirans()
    {
        return $this->hasMany(JurnalUmumLampiran::class);
    }

    /**
     * Get all of the jurnal umum's jurnals.
     */
    public function jurnals()
    {
        return $this->morphMany(Jurnal::class, 'jurnalable');
    }

    public function statusJurnalUmum()
    {
        return $this->belongsto(StatusJurnalUmum::class);
    }

    public function diterima()
    {
        return $this->jurnal_umum_status_id == STATUS_JURNAL_UMUM_DITERIMA;
    }

    public function scopeNeedPrintJkk($query)
    {
        return $query->where('status_jkk', 0);
    }

    public function getViewTglTransaksiAttribute()
    {
        return $this->tgl_transaksi->format('d F Y');
    }

    public function getTotalNominalDebetAttribute()
    {
        $totalDebet = $this->jurnalUmumItems->where('normal_balance_id', NORMAL_BALANCE_DEBET)->sum('nominal');
        
        return $totalDebet;
    }

    public function getTotalNominalDebetRupiahAttribute()
    {
        $totalDebet = $this->jurnalUmumItems->where('normal_balance_id', NORMAL_BALANCE_DEBET)->sum('nominal');
        
        return number_format($totalDebet,2,",",".");
    }

    public function getTotalNominalKreditRupiahAttribute()
    {
        $totalKredit = $this->jurnalUmumItems->where('normal_balance_id', NORMAL_BALANCE_KREDIT)->sum('nominal');
        
        return number_format($totalKredit,2,",",".");
    }

    public function getSerialNumberViewAttribute()
    {
        return 'TRU' . $this->tgl_transaksi->format('Y') . $this->tgl_transaksi->format('m') . str_pad($this->serial_number, 4, "0", STR_PAD_LEFT);
    }
}
