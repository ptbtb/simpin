<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = "t_pengajuan";
    protected $primaryKey = "kode_pengajuan";
    protected $dates = ['tgl_pengajuan', 'tgl_acc'];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota', 'kode_anggota');
    }

    public function jenisPinjaman()
    {
        return $this->belongsTo(JenisPinjaman::class, 'kode_jenis_pinjam', 'kode_jenis_pinjam');
    }
}
