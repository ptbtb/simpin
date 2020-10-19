<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    use HasFactory;

    protected $table = "t_simpan";
    protected $primaryKey = "kode_simpan";
    protected $dates = ['tgl_mulai', 'tgl_entri'];
    protected $appends = ['tanggal_entri', 'tanggal_mulai','besar_simpanan_rupiah'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    public function jenisSimpanan()
    {
        return $this->belongsTo(JenisSimpanan::class, 'kode_jenis_simpan', 'kode_jenis_simpan');
    }

    public function getTanggalEntriAttribute()
    {
        if ($this->tgl_entri)
        {
            return $this->tgl_entri->format('d M Y');
        }
        return $this->tgl_entri;
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
}
