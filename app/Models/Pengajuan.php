<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = "t_pengajuan";
    protected $primaryKey = "kode_pengajuan";
    protected $keyType = 'string';
    public $incrementing = false;
    protected $dates = ['tgl_pengajuan', 'tgl_acc'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota', 'kode_anggota');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function pinjaman()
    {
        return $this->hasOne(Pinjaman::class, 'kode_pengajuan_pinjaman');
    }

    public function paidByCashier()
    {
        return $this->belongsTo(User::class, 'paid_by_cashier');
    }

    public function jenisPinjaman()
    {
        return $this->belongsTo(JenisPinjaman::class, 'kode_jenis_pinjam', 'kode_jenis_pinjam');
    }

    public function statusPengajuan()
    {
        return $this->belongsTo(StatusPengajuan::class, 'id_status_pengajuan');
    }

    public function pengajuanTopup()
    {
        return $this->hasMany(PengajuanTopup::class, 'kode_pengajuan');
    }

    public function scopeNotApproved($query)
    {
        return $query->whereIn('id_status_pengajuan', [STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_KONFIRMASI, STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN]);
    }

    public function scopeNeedPrintJkk($query)
    {
        return $query->where('status_jkk', 0);
    }

    public function scopeMenungguPembayaran($query)
    {
        return $query->where('id_status_pengajuan', STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN);
    }

    public function menungguKonfirmasi()
    {
        return $this->id_status_pengajuan == STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_KONFIRMASI;
    }
    public function menungguApprovalSpv()
    {
        return $this->id_status_pengajuan == STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_SPV;
    }
    public function menungguApprovalAsman()
    {
        return $this->id_status_pengajuan == STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_ASMAN;
    }
    public function menungguApprovalManager()
    {
        return $this->id_status_pengajuan == STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_MANAGER;
    }
    public function menungguApprovalBendahara()
    {
        return $this->id_status_pengajuan == STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_BENDAHARA;
    }
    public function menungguApprovalKetua()
    {
        return $this->id_status_pengajuan == STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_KETUA;
    }

    public function menungguPembayaran()
    {
        return $this->id_status_pengajuan == STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN;
    }

    public function diterima()
    {
        return $this->id_status_pengajuan == STATUS_PENGAJUAN_PINJAMAN_DITERIMA;
    }

    public function jkkPrinted()
    {
        return $this->status_jkk == 1;
    }

    public function getViewJasaAttribute()
    {
        $value = round($this->jenisPinjaman->jasa*$this->besar_pinjam, 2);
        return 'Rp '.number_format($value, '2', ',', '.');
    }

    public function getViewAsuransiAttribute()
    {
        $value = round($this->jenisPinjaman->asuransi*$this->besar_pinjam, 2);
        return 'Rp '.number_format($value, '2', ',', '.');
    }

    public function getViewProvisiAttribute()
    {
        $value = round($this->jenisPinjaman->asuransi*$this->besar_pinjam, 2);
        return 'Rp '.number_format($value, '2', ',', '.');
    }

    public function getViewBesarPinjamanAttribute()
    {
        return 'Rp '.number_format($this->besar_pinjam, '2', ',', '.');
    }

    public function getViewBiayaAdminAttribute()
    {
        return 'Rp '.number_format($this->jenisPinjaman->biaya_admin, '2', ',', '.');
    }
}
