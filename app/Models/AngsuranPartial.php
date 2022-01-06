<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class AngsuranPartial extends Model implements Auditable
{
     use HasFactory;
    use Userstamps;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

     protected $table = "t_angsur_partial";
    protected $dates = ['deleted_at','updated_at','created_at','tgl_transaksi'];
    protected $appends = ['serial_number_view', 'created_at_view', 'created_by_view', 'updated_at_view', 'updated_by_view'];

    public function angsuran()
    {
        return $this->belongsTo(Angsuran::class, 'kode_angsur');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updatedBy that owns the Angsuran
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function jurnals()
    {
        return $this->morphMany(Jurnal::class, 'jurnalable');
    }

    public function getSerialNumberViewAttribute()
    {
        if ($this->tgl_transaksi){
            return 'ANG' . $this->tgl_transaksi->format('Y') . $this->tgl_transaksi->format('m') . str_pad($this->serial_number, 4, "0", STR_PAD_LEFT);
        }
        return false;
        
    }
    public function getCreatedAtViewAttribute()
    {
        return $this->created_at->format('d F Y');
    }

    public function getUpdatedAtViewAttribute()
    {
        return $this->updated_at->format('d F Y');
    }

    public function getCreatedByViewAttribute()
    {
        if ($this->createdBy)
        {
            return $this->createdBy->name;
        }
        return '-';
    }

    public function getUpdatedByViewAttribute()
    {
        if ($this->updatedBy)
        {
            return $this->updatedBy->name;
        }
        return '-';
    }
    public function akunKredit()
    {
        return $this->belongsTo(Code::class, 'id_akun_kredit');
    }
}
