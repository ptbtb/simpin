<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuBesarJurnal extends Model
{
    use HasFactory;
//    use \OwenIt\Auditing\Auditable;

    protected $table = "jurnal_buku_besar_v";
    protected $primaryKey = 'id';
    protected static function booted()
    {
        // you can do the same thing using anonymous function
        // let's add another scope using anonymous function
        static::addGlobalScope('real', function (Builder $builder) {
            $date = Carbon::parse(ActiveSaldoAwal::where('status', 1)->first()->tgl_saldo);
            return $builder->whereDate('tgl_transaksi', '>=', $date);
        });
    }

    public function anggota() {
        return $this->belongsTo(Anggota::class, 'kode_anggota','anggota');
    }



}
