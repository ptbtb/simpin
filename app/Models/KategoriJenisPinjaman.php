<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriJenisPinjaman extends Model
{
    use HasFactory;

    protected $table = 'kategori_jenis_pinjaman';
    protected $primaryKey = 'id';
    // protected $keyType = 'string';
    // public $incrementing = true;
}
