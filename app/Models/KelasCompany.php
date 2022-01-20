<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasCompany extends Model
{
    use HasFactory;

    protected $table = "t_kelas_company";
    protected $primaryKey = "id";

    /**
     * Get the Company that owns the KelasCompany
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get the kelasSimpanan associated with the KelasCompany
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function kelasSimpanan()
    {
        return $this->hasOne(kelasSimpanan::class, 'kelas_company_id', 'id');
    }

    public function jenisAnggota()
    {
        return $this->belongsTo(JenisAnggota::class, 'id_jenis_anggota');
    }
}
