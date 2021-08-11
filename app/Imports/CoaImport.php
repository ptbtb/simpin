<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Code;
use App\Models\CodeCategory;
use App\Models\CodeType;
use App\Models\NormalBalance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class CoaImport implements OnEachRow
{
    /**
    * @param Collection $collection
    */
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        if ($rowIndex == 1)
        {
            return null;
        }

        
        $coa = Code::where('code',$row[1])->first();
        $code_type= CodeType::where('name',$row[3])->first();
        $normal_balance = NormalBalance::where('name',$row[5])->first();
        $code_category = CodeCategory::where('name',$row[4])->first();
        if($coa){
            $coa->CODE=$row[1];
            $coa->code_type_id=$code_type->id;
            $coa->normal_balance_id=$normal_balance->id;
            $coa->code_category_id=$code_category->id;
            $coa->NAMA_TRANSAKSI=$row[2];
            $coa->is_parent=(strtoupper($row[6])=='INDUK' )?1:0;
            $coa->save();
        }else{
           $coa = new Code();
           $coa->u_entry=Auth::user()->name;
           $coa->CODE=$row[1];
           $coa->code_type_id=$code_type->id;
           $coa->normal_balance_id=$normal_balance->id;
           $coa->code_category_id=$code_category->id;
           $coa->NAMA_TRANSAKSI=$row[2];
           $coa->is_parent=(strtoupper($row[6])=='INDUK' )?1:0;
           $coa->save();
       }

       


       return $coa;
   }
}
