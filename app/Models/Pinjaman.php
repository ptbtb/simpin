<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = "t_pinjam";
    protected $primaryKey = "kode_pinjam";
    protected $dates = ['tgl_entri', 'tgl_tempo'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    public function jenisPinjaman()
    {
        return $this->belongsTo(JenisPinjaman::class,'kode_jenis_pinjam');
    }

    public function listAngsuran()
    {
        return $this->hasMany(Angsuran::class, 'kode_pinjam');
    }
}
