<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Angsuran extends Model implements Auditable
{
    use HasFactory;
    use Userstamps;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;


    protected $table = "t_angsur";
    protected $primaryKey = "kode_angsur";
    protected $dates = ['tgl_entri','paid_at','jatuh_tempo'];
    protected $appends = ['serial_number_view', 'created_at_view', 'created_by_view', 'updated_at_view', 'updated_by_view'];
    protected static function booted()
    {
        // you can do the same thing using anonymous function
        // let's add another scope using anonymous function
        static::addGlobalScope('real', function (Builder $builder) {
            $date = Carbon::parse(ActiveSaldoAwal::where('status', 1)->first()->tgl_saldo);
            return $builder->whereDate('tgl_transaksi', '>=', $date);
        });
    }

    public function statusAngsuran()
    {
        return $this->belongsTo(StatusAngsuran::class, 'id_status_angsuran');
    }

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'kode_pinjam', 'kode_pinjam');
    }

    public function angsuranPartial()
    {
        return $this->hasMany(AngsuranPartial::class, 'kode_angsur');
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
        if ($this->tgl_transaksi){
            return 'ANG' .  Carbon::createFromFormat('Y-m-d',$this->tgl_transaksi)->format('Y') . Carbon::createFromFormat('Y-m-d',$this->tgl_transaksi)->format('m') . str_pad($this->serial_number, 4, "0", STR_PAD_LEFT);
        }
        return '-';

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

    /**
     * Get the totalPembayaran
     *
     * @param  string  $value
     * @return string
     */
    public function getTotalPembayaranAttribute()
    {
        return $this->besar_pembayaran + $this->besar_pembayaran_jasa;
    }

    public function anggota() {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }
}
