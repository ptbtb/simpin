<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penarikan extends Model
{
    use HasFactory;

    protected $table = "t_pengambilan";
    protected $primaryKey = "kode_ambil";
    protected $dates = ['tgl_ambil', 'tgl_acc'];
    protected $fillable = ['kode_anggota', 'kode_tabungan','besar_ambil','tgl_ambil','keterangan','code_trans','u_entry'];
    protected $appends = ['serial_number_view', 'created_at_view', 'updated_at_view', 'created_by_view', 'updated_by_view'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    public function tabungan()
    {
        return $this->belongsTo(Tabungan::class, 'id_tabungan');
    }
    public function jenisSimpanan()
    {
        return $this->belongsTo(jenisSimpanan::class, 'code_trans');
    }

    public function statusPenarikan()
    {
        return $this->belongsTo(StatusPenarikan::class, 'status_pengambilan');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidByCashier()
    {
        return $this->belongsTo(User::class, 'paid_by_cashier');
    }

    /**
     * Get all of the penarikan's jurnals.
    */
    public function jurnals()
    {
        return $this->morphMany(Jurnal::class, 'jurnalable');
    }

    public function akunDebet()
    {
        return $this->belongsTo(Code::class, 'id_akun_debet');
    }

    public function scopeNotApproved($query)
    {
        return $query->whereIn('status_pengambilan', [STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI, STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN]);
    }

    public function scopeNeedPrintJkk($query)
    {
        return $query->where('status_jkk', 0);
    }

    public function scopeMenungguPembayaran($query)
    {
        return $query->where('status_pengambilan', STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN);
    }
    
    /**
     * Scope a query to only include Approved
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status_pengambilan', STATUS_PENGAMBILAN_DITERIMA);
    }

    public function menungguKonfirmasi()
    {
        return $this->status_pengambilan == STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI;
    }
    public function menungguApprovalSpv()
    {
        return $this->status_pengambilan == STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_SPV;
    }
    public function menungguApprovalAsman()
    {
        return $this->status_pengambilan == STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_ASMAN;
    }
    public function menungguApprovalManager()
    {
        return $this->status_pengambilan == STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_MANAGER;
    }
    public function menungguApprovalBendahara()
    {
        return $this->status_pengambilan == STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_BENDAHARA;
    }
    public function menungguApprovalKetua()
    {
        return $this->status_pengambilan == STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_KETUA;
    }

    public function menungguPembayaran()
    {
        return $this->status_pengambilan == STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN;
    }

    public function diterima()
    {
        return $this->status_pengambilan == STATUS_PENGAMBILAN_DITERIMA;
    }

    public function jkkPrinted()
    {
        return $this->status_jkk == 1;
    }

    public function getSerialNumberViewAttribute()
    {
        return 'TAR' . $this->tgl_ambil->format('Y') . $this->tgl_ambil->format('m') . str_pad($this->serial_number, 4, "0", STR_PAD_LEFT);
    }

    /**
     * Get the createdAtView
     *
     * @param  string  $value
     * @return string
     */
    public function getCreatedAtViewAttribute()
    {
        return $this->created_at->format('d F Y');
    }

    /**
     * Get the getUpdatedAtView
     *
     * @param  string  $value
     * @return string
     */
    public function getUpdatedAtViewAttribute()
    {
        return $this->updated_at->format('d F Y');
    }

    /**
     * Get the createdByView
     *
     * @param  string  $value
     * @return string
     */
    public function getCreatedByViewAttribute()
    {
        if ($this->createdBy)
        {
            return $this->createdBy->name;
        }
        return '-';
    }

    /**
     * Get the updatedByView
     *
     * @param  string  $value
     * @return string
     */
    public function getUpdatedByViewAttribute($value)
    {
        if ($this->paidByCashier)
        {
            return $this->paidByCashier->name;
        }
        elseif($this->approvedBy)
        {
            return $this->approvedBy->name;
        }
        return '-';
    }
}
