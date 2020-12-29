<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPinjaman extends Model
{
    use HasFactory;

    protected $table = "t_jenis_pinjam";
    protected $primaryKey = "kode_jenis_pinjam";
    protected $keyType = 'string';
    public $incrementing = false;
    protected $dates = ['tgl_entry'];
    
    public function kategoriJenisPinjaman()
    {
        return $this->belongsTo(KategoriJenisPinjaman::class, 'kategori_jenis_pinjaman_id');
    }

    public function tipeJenisPinjaman()
    {
        return $this->belongsTo(TipeJenisPinjaman::class, 'tipe_jenis_pinjaman_id');
    }

    public function listPinjaman()
    {
        return $this->hasMany(Pinjaman::class,'kode_jenis_pinjam');
    }

    public function isDanaKopegmar()
    {
        return $this->tipe_jenis_pinjaman_id == TIPE_JENIS_PINJAMAN_DANA_KOPEGMAR;
    }

    public function isDanaLain()
    {
        return $this->tipe_jenis_pinjaman_id == TIPE_JENIS_PINJAMAN_DANA_LAIN;
    }

    public function isJangkaPendek()
    {
        return $this->kategori_jenis_pinjaman_id == KATEGORI_JENIS_PINJAMAN_JANGKA_PENDEK;
    }

    public function isJangkaPanjang()
    {
        return $this->kategori_jenis_pinjaman_id == KATEGORI_JENIS_PINJAMAN_JANGKA_PANJANG;
    }
}
