<?php

namespace App\Imports;

use App\Models\Budget;
use App\Models\Code;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class BudgetImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row => $value)
        {
            if ($row == 0)
            {
                continue;
            }

            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value[2])->format('Y-m-d');
            $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
            $code = Code::where('CODE', 'like', '%' . $value[0] . '%')
                        ->first();
            
            if (is_null($code))
            {
                continue;
            }

            $budget = new Budget();
            $budget->code = $code->CODE;
            $budget->name = $code->NAMA_TRANSAKSI;
            $budget->date = $carbonDate;
            $budget->description = $value[4];
            $budget->amount = $value[3];
            $budget->created_by = Auth::user()->id;
            $budget->save();
        }
    }
}
