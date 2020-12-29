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

    public function kelasCompany()
    {
        return $this->belongsTo(KelasCompany::class, 'kelas_company_id', 'id');
    }
}
