<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Anggota extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "t_anggota";
    protected $primaryKey = 'kode_anggota';
    protected $appends = ['kode_anggota_prefix', 'unit_kerja','tgl_lahir_view'];
    public $incrementing = false;
    public $dates = ['tgl_lahir', 'tgl_masuk'];
    protected $fillable = ['kode_anggota',
        // 'kode_tabungan',
        'company_id',
        'id_jenis_anggota',
        'kelas_company_id',
        'tgl_masuk',
        'nama_anggota',
        'jenis_kelamin',
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
        return $this->hasOne(User::class, 'kode_anggota', 'kode_anggota');
    }

    public function listPenghasilan()
    {
        return $this->hasMany(Penghasilan::class, 'kode_anggota');
    }

    public function jenisAnggota()
    {
        return $this->belongsTo(JenisAnggota::class, 'id_jenis_anggota', 'id_jenis_anggota');
    }

    public function tabungan()
    {
        return $this->hasMany(Tabungan::class, 'kode_anggota');
    }
    public function simpanSaldoAwal()
    {
        return $this->hasMany(View\ViewSimpanSaldoAwal::class, 'kode_anggota');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function kelasCompany()
    {
        return $this->belongsTo(KelasCompany::class, 'kelas_company_id', 'id');
    }

    public function listPinjaman()
    {
        return $this->hasMany(Pinjaman::class, 'kode_anggota');
    }

    public function listPenarikan()
    {
        return $this->hasMany(Penarikan::class, 'kode_anggota');
    }

    public function listSimpanan()
    {
        return $this->hasMany(Simpanan::class, 'kode_anggota');
    }

    public function Bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank');
    }
    public function getTglLahirViewAttribute()
    {
        return $this->tgl_lahir->format('d M Y');
    }

    public function getKodeAnggotaPrefixAttribute()
    {
        if ($this->jenisAnggota)
        {
            return $this->jenisAnggota->prefix.' - '.$this->kode_anggota;
        }
        else
        {
            return $this->kode_anggota;
        }
    }

    public function getNoKtpAttribute()
    {
        if (is_numeric($this->ktp))
        {
            return $this->ktp;
        }
    }

    public function isAnggotaBiasa()
    {
        return $this->id_jenis_anggota == JENIS_ANGGOTA_BIASA;
    }

    public function isAnggotaLuarBiasa()
    {
        return $this->id_jenis_anggota == JENIS_ANGGOTA_LUAR_BIASA;
    }

    public function isPensiunan()
    {
        return $this->id_jenis_anggota == JENIS_ANGGOTA_PENSIUNAN;
    }

    public function getUnitKerjaAttribute()
    {
        if ($this->company)
        {
            return $this->company->nama;
        }

        return '-';
    }
}
