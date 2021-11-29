<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class Angsuran extends Model
{
    use HasFactory;
    use Userstamps;

    protected $table = "t_angsur";
    protected $primaryKey = "kode_angsur";
    protected $dates = ['tgl_entri','paid_at','jatuh_tempo','tgl_transaksi'];
    protected $appends = ['serial_number_view', 'created_at_view', 'created_by_view', 'updated_at_view', 'updated_by_view'];

    public function statusAngsuran()
    {
        return $this->belongsTo(StatusAngsuran::class, 'id_status_angsuran');
    }

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'kode_pinjam', 'kode_pinjam');
    }

    /**
     * Get the createdBy that owns the Angsuran
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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

    /**
     * Get all of the angsuran's jurnals.
    */
    public function jurnals()
    {
        return $this->morphMany(Jurnal::class, 'jurnalable');
    }

    public function akunKredit()
    {
        return $this->belongsTo(Code::class, 'id_akun_kredit');
    }

    public function getTotalAngsuranAttribute()
    {
        return $this->besar_angsuran + $this->jasa - $this->diskon;
    }

    public function getSisaPinjamanAttribute()
    {
        if ($this->besar_pembayaran > $this->jasa)
        {
            return $this->sisa_pinjam + $this->totalAngsuran - $this->besar_pembayaran;
        }
        return $this->sisa_pinjam + $this->besar_angsuran;
    }

    public function getSerialNumberViewAttribute()
    {
        return 'ANG' . $this->tgl_entri->format('Y') . $this->tgl_entri->format('m') . str_pad($this->serial_number, 4, "0", STR_PAD_LEFT);
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

    public function isLunas()
    {
        return $this->id_status_angsuran == STATUS_ANGSURAN_LUNAS;
    }
    public function menungguApproval()
    {
        return $this->id_status_angsuran == STATUS_ANGSURAN_MENUNGGU_APPROVAL;
    }
}
