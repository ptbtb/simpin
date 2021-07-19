<?php

namespace App\Imports;

use App\Models\Anggota;
use App\Models\SHU;
use App\Models\SHUDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class SHUImport implements ToCollection
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        /**
         * kode anggota         : 0
         * saldo awal           : 1-5
         * simpanan pokok       : 6-18
         * ambil pokok          : 19-31
         * simpanan wajib       : 32-44
         * ambil wajib          : 45-57
         * simpanan sukarela    : 58-70
         * ambil sukarela       : 71-83
         * simpanan khusus      : 84-96
         * ambil khusus         : 97-109
         * saldo pws            : 115 - 127
         * shu pws              : 128 - 139
         * saldo khusu          : 141 - 153
         * shu khusu            : 154 - 166
         */
        $year = $this->year;
        foreach ($collection as $row => $col)
        {
            /**
             * create shu header
             * create shu detail saldo awal
             * create shu detial perbulan
             * create shu detail jumlah
             */

            //  create shu header
            if ($row > 1)
            {
                $anggota = Anggota::find($col[0]);
                if ($anggota)
                {
                    DB::transaction(function () use ($anggota, $year, $col)
                    {
                        // create shu header
                        $shu = new SHU();
                        $shu->company_id = $anggota->company_id;
                        $shu->kode_anggota = $anggota->kode_anggota;
                        $shu->year = $year;
                        $shu->save();

                        // create shu detail saldo awal
                        $shuDetail = new SHUDetail();
                        $shuDetail->shu_id = $shu->id;
                        $shuDetail->shu_detail_type_id = SHU_DETAIL_TYPE_SALDO_AWAL;
                        $shuDetail->month = null;
                        $shuDetail->pokok = $col[1];
                        $shuDetail->wajib = $col[2];
                        $shuDetail->sukarela = $col[3];
                        $shuDetail->saldo_pws = $col[1] + $col[2] + $col[3];
                        $shuDetail->saldo_khusus = $col[4];
                        $shuDetail->save();

                        // create shu detial perbulan
                        for ($i=1; $i <= 12; $i++)
                        {
                            $shuDetail = new SHUDetail();
                            $shuDetail->shu_id = $shu->id;
                            $shuDetail->shu_detail_type_id = SHU_DETAIL_TYPE_BULAN;
                            $shuDetail->month = $i;
                            $shuDetail->pokok = $col[6 + $i - 1] - $col[19 + $i - 1];
                            $shuDetail->wajib = $col[32 + $i - 1] - $col[45 + $i - 1];
                            $shuDetail->sukarela = $col[58 + $i - 1] - $col[71 + $i - 1];
                            $shuDetail->saldo_pws = $col[115 + $i - 1];
                            $shuDetail->shu_pws = $col[128 + $i - 1];
                            $shuDetail->saldo_khusus = $col[141 + $i - 1];
                            $shuDetail->shu_khusus = $col[154 + $i - 1];
                            $shuDetail->cashback = $col[167 + $i - 1];
                            $shuDetail->kontribusi = $col[180];
                            $shuDetail->total_shu_sebelum_pajak = $col[182 + $i - 1];
                            $shuDetail->pajak_pph = $col[195 + $i - 1];
                            $shuDetail->total_shu_setelah_pajak = $col[208 + $i - 1];
                            $shuDetail->shu_disimpan = $col[234+ $i - 1];
                            $shuDetail->shu_dibagi = $col[221 + $i - 1];
                            $shuDetail->save();
                        }

                        // create shu detail jumlah
                        $i = 13;
                        $shuDetail = new SHUDetail();
                        $shuDetail->shu_id = $shu->id;
                        $shuDetail->shu_detail_type_id = SHU_DETAIL_TYPE_JUMLAH;
                        $shuDetail->month = null;
                        $shuDetail->pokok = $col[110];
                        $shuDetail->wajib = $col[111];
                        $shuDetail->sukarela = $col[112];
                        $shuDetail->saldo_pws = $col[115 + $i - 1];
                        $shuDetail->shu_pws = $col[128 + $i - 1];
                        $shuDetail->saldo_khusus = $col[141 + $i - 1];
                        $shuDetail->shu_khusus = $col[154 + $i - 1];
                        $shuDetail->cashback = $col[167 + $i - 1];
                        $shuDetail->kontribusi = $col[180];
                        $shuDetail->total_shu_sebelum_pajak = $col[182 + $i - 1];
                        $shuDetail->pajak_pph = $col[195 + $i - 1];
                        $shuDetail->total_shu_setelah_pajak = $col[208 + $i - 1];
                        $shuDetail->shu_disimpan = $col[234+ $i - 1];
                        $shuDetail->shu_dibagi = $col[221 + $i - 1];
                        $shuDetail->save();
                    });
                }
            }
        }
    }
}
