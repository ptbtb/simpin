<?php

namespace App\Imports;

use App\Managers\JurnalManager;
use App\Models\SaldoAwal;
use App\Models\Code;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SaldoAwalImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        try
        {
            $saldoAwal = true;
            $codeId = null;
            $rowIndex = $row->getIndex();
            $row      = $row->toArray();
            if ($rowIndex == 1)
            {
                return null;
            }

            if($row[0] == "\N" || $row[0] == '' || $row[0] == null)
            {
                $codeId = null;
            }
            else
            {
                $code = Code::where('CODE', $row[0])
                            ->where('is_parent', 0)
                            ->where('CODE', 'not like', "411%")
                            ->where('CODE', 'not like', "106%")
                            ->where('CODE', 'not like', "502%")
                            ->where('CODE', 'not like', "105%")
                            ->doesntHave('saldoAwals')
                            ->first();

                if( isset($code) && $code->is_parent == 0)
                {
                    $codeId = $code->id;
                }
            }
            \Log::error($codeId);
            $batch = ($row[2] == "\N" || $row[2] == '' || $row[2] == null)? null:Carbon::parse(Date::excelToDateTimeObject($row[2]))->format('Y-m-d');
//            dd($batch);
            if($codeId != null && $batch != null)
            {

                $fields = [
                    'code_id' => $codeId,
                    'nominal' => ($row[1] == "\N" || $row[1] == '' || $row[1] == null)? 0:$row[1],
                    'batch' => $batch,
                ];

                $saldoAwal = SaldoAwal::create($fields);
               JurnalManager::createSaldoAwal($saldoAwal);
            }

            return $saldoAwal;
        }
        catch (\Throwable $th)
        {
            dd($th);
            \Log::error($th);
            return redirect()->back()->withError('Gagal menyimpan data');
        }
    }
}
