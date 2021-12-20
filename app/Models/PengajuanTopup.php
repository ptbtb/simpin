<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PengajuanTopup extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;


    protected $table = 't_pengajuan_topup';

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'kode_pinjaman', 'kode_pinjam');
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'kode_pengajuan', 'kode_pengajuan');
    }
}
