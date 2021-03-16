<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalUmum extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "t_jurnal_umum";
    protected $dates = ['tgl_transaksi'];
    protected $appends = ['view_tgl_transaksi'];

    public function jurnalUmumItems()
    {
        return $this->hasMany(JurnalUmumItem::class);
    }

    public function jurnalUmumLampirans()
    {
        return $this->hasMany(JurnalUmumLampiran::class);
    }

    public function getViewTglTransaksiAttribute()
    {
        return $this->tgl_transaksi->format('d F Y');
    }
}
