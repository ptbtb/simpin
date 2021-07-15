<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SHUDetail extends Model
{
    use HasFactory;

    protected $table = 'shu_detail';

    /**
     * Get the shu that owns the SHUDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shu()
    {
        return $this->belongsTo(SHU::class, 'shu_id');
    }

    /**
     * Get the shuDetailType that owns the SHUDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shuDetailType()
    {
        return $this->belongsTo(SHUDetailType::class, 'shu_detail_type_id');
    }

    public function isSaldoAwal()
    {
        return $this->shu_detail_type_id == SHU_DETAIL_TYPE_SALDO_AWAL;
    }

    public function isJumlah()
    {
        return $this->shu_detail_type_id == SHU_DETAIL_TYPE_JUMLAH;
    }
}
