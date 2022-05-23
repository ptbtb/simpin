<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinjamanRestruktur extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pinjaman_restrukturisasi';

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'kode_pinjam', 'kode_pinjam');
    }
}
