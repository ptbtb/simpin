<?php

namespace App\Imports;

use App\Models\Anggota;
use App\Models\SHU;
use App\Models\SHUDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SHUImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        // $anggota = Anggota::has('company')->find($collection[0][1]);
        $anggota = Anggota::find($collection[0][1]);
        if (is_null($anggota))
        {
            return false;
        }

        $shu = new SHU();
        $shu->company_id = $anggota->company_id;
        $shu->kode_anggota = $anggota->kode_anggota;
        $shu->year = $collection[1][1];
        $shu->save();

        foreach ($collection as $key => $value)
        {
            if ($key == 4)
            {
                $shuDetail = new SHUDetail();
                $shuDetail->shu_id = $shu->id;
                $shuDetail->shu_detail_type_id = SHU_DETAIL_TYPE_SALDO_AWAL;
                $shuDetail->month = null;
                $shuDetail->pokok = $value[1];
                $shuDetail->wajib = $value[2];
                $shuDetail->sukarela = $value[3];
                $shuDetail->saldo_pws = $value[4];
                $shuDetail->shu_pws = $value[5];
                $shuDetail->saldo_khusus = $value[6];
                $shuDetail->shu_khusus = $value[7];
                $shuDetail->cashback = 0;
                $shuDetail->kontribusi = 0;
                $shuDetail->total_shu_sebelum_pajak = $value[5] + $value[7];
                $shuDetail->pajak_pph = $value[8];
                $shuDetail->total_shu_setelah_pajak = $value[5] + $value[7] - $value[8];
                $shuDetail->shu_disimpan = ($value[5] + $value[7] - $value[8])*25/100;
                $shuDetail->shu_dibagi =  ($value[5] + $value[7] - $value[8])*75/100;
                $shuDetail->save();
            }
            elseif($key == 17)
            {
                $shuDetail = new SHUDetail();
                $shuDetail->shu_id = $shu->id;
                $shuDetail->shu_detail_type_id = SHU_DETAIL_TYPE_JUMLAH;
                $shuDetail->month = null;
                $shuDetail->pokok = $value[1];
                $shuDetail->wajib = $value[2];
                $shuDetail->sukarela = $value[3];
                $shuDetail->saldo_pws = $value[4];
                $shuDetail->shu_pws = $value[5];
                $shuDetail->saldo_khusus = $value[6];
                $shuDetail->shu_khusus = $value[7];
                $shuDetail->cashback = 0;
                $shuDetail->kontribusi = 0;
                $shuDetail->total_shu_sebelum_pajak = $value[5] + $value[7];
                $shuDetail->pajak_pph = $value[8];
                $shuDetail->total_shu_setelah_pajak = $value[5] + $value[7] - $value[8];
                $shuDetail->shu_disimpan = ($value[5] + $value[7] - $value[8])*25/100;
                $shuDetail->shu_dibagi =  ($value[5] + $value[7] - $value[8])*75/100;
                $shuDetail->save();
            }
            elseif($key > 4)
            {
                $shuDetail = new SHUDetail();
                $shuDetail->shu_id = $shu->id;
                $shuDetail->shu_detail_type_id = SHU_DETAIL_TYPE_BULAN;
                $shuDetail->month = $value[0];
                $shuDetail->pokok = $value[1];
                $shuDetail->wajib = $value[2];
                $shuDetail->sukarela = $value[3];
                $shuDetail->saldo_pws = $value[4];
                $shuDetail->shu_pws = $value[5];
                $shuDetail->saldo_khusus = $value[6];
                $shuDetail->shu_khusus = $value[7];
                $shuDetail->cashback = 0;
                $shuDetail->kontribusi = 0;
                $shuDetail->total_shu_sebelum_pajak = $value[5] + $value[7];
                $shuDetail->pajak_pph = $value[8];
                $shuDetail->total_shu_setelah_pajak = $value[5] + $value[7] - $value[8];
                $shuDetail->shu_disimpan = ($value[5] + $value[7] - $value[8])*25/100;
                $shuDetail->shu_dibagi =  ($value[5] + $value[7] - $value[8])*75/100;
                $shuDetail->save();
            }
        }
    }
}
