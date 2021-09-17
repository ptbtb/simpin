<?php

namespace App\Imports;

use App\Models\SaldoAwal;
use App\Models\Code;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

use Illuminate\Support\Facades\Log;

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
            if($codeId != null)
            {
                 
                $fields = [
                    'code_id' => $codeId,
                    'nominal' => ($row[1] == "\N" || $row[1] == '' || $row[1] == null)? 0:$row[1],
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
