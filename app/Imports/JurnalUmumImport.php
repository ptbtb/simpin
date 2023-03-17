<?php

namespace App\Imports;

use App\Models\Code;
use App\Models\JurnalUmum;
use App\Models\JurnalUmumItem;
use App\Models\TransferredSHU;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class JurnalUmumImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $value)
        {
            // dd($key, $value);
            if ($key > 0)
            {
                $user = Auth::user();
                $posting=Carbon::parse(Date::excelToDateTimeObject($value[1]));

                $ju = JurnalUmum::where('no_ju_import', $value[0]);
                if ($ju->count() > 0){
                    $ju = $ju->first();
                } else {
                    $ju = new JurnalUmum();
                    $ju->no_ju_import = $value[0];
                    $ju->tgl_transaksi = $posting;
                    $ju->deskripsi = $value[2];
                    $ju->tgl_acc = $posting;
//                    $ju->kode_anggota = $value[6];
                    $ju->status_jkk = 1;
                    $ju->import = 1;
                    $ju->status_jurnal_umum_id = 8;
                    $ju->paid_by_cashier = $user->id;
                    $ju->approved_by = $user->id;
                    $ju->save();
                }

                $jui = new JurnalUmumItem();
                $jui->jurnal_umum_id = $ju->id;
                $jui->code_id = Code::where('CODE', $value[3])->pluck('id')->first();
                $jui->normal_balance_id = ($value[4] == 'D') ? NORMAL_BALANCE_DEBET : NORMAL_BALANCE_KREDIT;
                $jui->nominal = $value[5];
                $jui->kode_anggota = $value[6];
                $jui->save();
            }
        }
    }
}
