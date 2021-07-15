<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SHU extends Model
{
    use HasFactory;

    protected $table = 'shu';

    /**
     * Get the anggota that owns the SHU
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    /**
     * Get all of the shuDetails for the SHU
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shuDetails()
    {
        return $this->hasMany(SHUDetail::class, 'shu_id');
    }
}
