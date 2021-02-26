<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanTopup extends Model
{
    use HasFactory;

    protected $table = 't_pengajuan_topup';

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'kode_pinjaman');
    }
}