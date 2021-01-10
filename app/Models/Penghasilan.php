<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penghasilan extends Model
{
    use HasFactory;

    protected $table = 't_penghasilan';

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    public function jenisPenghasilan()
    {
        return $this->belongsTo(JenisPenghasilan::class, 'id_jenis_penghasilan');
    }

    public function scopePenghasilanTertentu($query)
    {
        return $query->whereHas('jenisPenghasilan', function ($q)
        {
            return $q->where('is_penghasilan_tertentu',1);
        });
    }
}
