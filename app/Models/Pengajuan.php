<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = "t_pengajuan";
    //protected $primaryKey = "kode_pengajuan";
    //protected $keyType = 'string';
    public $incrementing = false;
    protected $dates = ['tgl_pengajuan', 'tgl_acc'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['created_at_view', 'updated_at_view', 'created_by_view', 'updated_by_view'];

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
        return $this->hasOne(Pinjaman::class, 'kode_pengajuan_pinjaman','kode_pengajuan');
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
        return $this->hasMany(PengajuanTopup::class, 'kode_pengajuan', 'kode_pengajuan');
    }

    public function akunDebet()
    {
        return $this->belongsTo(Code::class, 'id_akun_debet');
    }

    /**
     * Get the jenisPenghasilan that owns the Pengajuan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jenisPenghasilan()
    {
        return $this->belongsTo(JenisPenghasilan::class, 'sumber_dana');
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
        $value = round($this->jenisPinjaman->provisi*$this->besar_pinjam, 2);
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

    public function getViewJasaPelunasanDipercepatAttribute()
    {
        return 'Rp '.number_format($this->pengajuanTopup->sum('jasa_pelunasan_dipercepat'), '2', ',', '.');
    }

    public function getViewSisaPinjamanAttribute()
    {
        $hasil = $this->pengajuanTopup->sum('total_bayar_pelunasan_dipercepat')-$this->pengajuanTopup->sum('jasa_pelunasan_dipercepat');
        return 'Rp '.number_format($hasil, '2', ',', '.');
    }

    public function getViewCreditBankAttribute()
    {
        $provisi = round($this->jenisPinjaman->provisi*$this->besar_pinjam, 2);
        $admin = $this->jenisPinjaman->biaya_admin;
        $asuransi = round($this->jenisPinjaman->asuransi*$this->besar_pinjam, 2);
        $total = $this->besar_pinjam - $provisi - $admin - $asuransi;

        return 'Rp '.number_format($total, '2', ',', '.');
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
