<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class JenisAnggota extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "t_jenis_anggota";
    protected $primaryKey = 'id_jenis_anggota';

    public function anggotas()
    {
        return $this->hasMany(Anggota::class, 'id_jenis_anggota', 'id_jenis_anggota');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'create_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'update_by');
    }
}
