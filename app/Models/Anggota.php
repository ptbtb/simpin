<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;
    protected $table = "t_anggota";
    protected $primaryKey = 'kode_anggota';
    public $incrementing = false;
    protected $fillable = ['kode_anggota',
        'kode_tabungan',
        'tgl_masuk',
        'nama_anggota',
        'tempat_lahir',
        'tgl_lahir',
        'alamat_anggota',
        'telp',
        'lokasi_kerja',
        'u_entry',
        'ktp',
        'nipp',
        'no_rek',
        'email',
        'emergency_kontak',
        'status',
        ];

    public function user()
    {
        return $this->hasOne(User::class, 'kode_anggota');
    }    
}
