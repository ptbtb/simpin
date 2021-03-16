<?php

namespace App\Models\View;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewSimpanSaldoAwal extends Model
{
    use HasFactory;

    protected $table = "v_simpin_saldo_awal";
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }
}
