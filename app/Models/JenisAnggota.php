<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisAnggota extends Model
{
    use HasFactory;

    protected $table = "t_jenis_anggota";

    public function anggotas()
    {
        return $this->hasMany(Anggota::class, 'id_jenis_anggota', 'id_jenis_anggota');
    }
}
