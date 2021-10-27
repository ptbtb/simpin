<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSimpanan extends Model
{
    use HasFactory;
    protected $table = "t_jenis_simpan";
    protected $primaryKey = "kode_jenis_simpan";
    protected $keyType = 'string';
    protected $dates = ['tgl_entri'];
    protected $appends = ['view_nama'];
    public $timestamps = false;
    protected $fillable= [
        'kode_jenis_simpan',
        'nama_simpanan',
        'besar_simpanan',
        'tgl_tagih',
        'hari_jatuh_tempo',
        'u_entry',
        'tgl_entri'
    ];

    public function getViewNamaAttribute()
    {
        return strtoupper($this->nama_simpanan);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required',1);
    }

    public function listTabungan()
    {
        return $this->hasMany(Tabungan::class, 'kode_trans', 'kode_jenis_simpan');
    }
}
