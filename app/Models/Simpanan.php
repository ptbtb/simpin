<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Simpanan extends Model implements Auditable
{
    use HasFactory;
     use \OwenIt\Auditing\Auditable;
      use SoftDeletes;
     use Userstamps;

    protected $table = "t_simpan";
    protected $primaryKey = "kode_simpan";
    protected $dates = ['tgl_mulai', 'tgl_entri', 'periode','deleted_at'];
    protected $appends = ['tanggal_entri', 'tanggal_mulai','besar_simpanan_rupiah', 'serial_number_view', 'status_simpanan_view','periode_view'];
    protected $fillable = ['jenis_simpan', 'besar_simpanan','kode_anggota','u_entry','tgl_mulai','tgl_entri','kode_jenis_simpan','keterangan'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    public function jenisSimpanan()
    {
        return $this->belongsTo(JenisSimpanan::class, 'kode_jenis_simpan', 'kode_jenis_simpan');
    }

    public function akunDebet()
    {
        return $this->belongsTo(Code::class, 'id_akun_debet');
    }

    /**
     * Get all of the simpanan's jurnals.
    */
    public function jurnals()
    {
        return $this->morphMany(Jurnal::class, 'jurnalable');
    }

    public function statusSimpanan()
    {
        return $this->belongsTo(StatusSimpanan::class, 'id_status_simpanan');
    }

    public function getTanggalEntriAttribute()
    {
        if ($this->tgl_entri)
        {
            return $this->tgl_entri->format('d M Y');
        }
        return $this->tgl_entri;
    }
    public function getPeriodeViewAttribute()
    {
        if ($this->periode)
        {
            return $this->periode->format('Y-m');
        }
        return $this->periode;
    }

    public function getTanggalMulaiAttribute()
    {
        if ($this->tgl_mulai)
        {
            return $this->tgl_mulai->format('d M Y');
        }
        return $this->tgl_mulai;
    }

    public function getBesarSimpananRupiahAttribute()
    {
        if ($this->besar_simpanan)
        {
            return 'Rp.' . number_format($this->besar_simpanan,0,",",".");
        }
        return $this->besar_simpanan;
    }

    public function getSerialNumberViewAttribute()
    {
        return 'SIP' . $this->tgl_entri->format('Y') . $this->tgl_entri->format('m') . str_pad($this->serial_number, 4, "0", STR_PAD_LEFT);
    }

    public function getStatusSimpananViewAttribute()
    {
        return $this->statusSimpanan->name;
    }
}
