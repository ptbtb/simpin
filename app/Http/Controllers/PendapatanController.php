<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\CodeCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendapatanController extends Controller
{
    public function laporan(Request $request)
    {
        $years = range(Carbon::now()->year, 2000);
        $data['title'] = 'Laporan Pendapatan';
        $data['years'] = $years;
        $data['request'] = $request;

        $year = $request->year;
        // $year = 2021;

        if ($year)
        {
            $groupLabaRugi = CodeCategory::where('name', 'like', 'PENDAPATAN%')
                                            ->get();

            $codes = Code::where('is_parent', 0)
                            /*->where(function ($query) use ($groupLabaRugi)
                            {
                                for ($i = 0; $i < count($groupLabaRugi); $i++)
                                {
                                    $query->orWhere('code_category_id',  $groupLabaRugi[$i]->id);
                                }
                            })*/
                            ->whereIn('code_category_id', $groupLabaRugi->pluck('id'))
                            ->get();
            $groupCodes = $codes->groupBy(function ($item, $key)
                                {
                                    return $item['CODE'];
                                    // return substr($item['CODE'], 0, 8);
                                });
            $pendapatan = collect();

            foreach ($groupCodes as $key => $groupCode)
            {
                $saldo = 0;
                foreach ($groupCode as $code)
                {
                    // get code's normal balance
                    if($code->normal_balance_id == NORMAL_BALANCE_DEBET)
                    {
                        // period's month
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereYear('created_at', $year)->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereYear('created_at', $year)->sum('kredit');

                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;
                    }
                    else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                    {
                        // period's month
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereYear('created_at', $year)->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereYear('created_at', $year)->sum('kredit');

                        $saldo -= $saldoDebet;
                        $saldo += $saldoKredit;
                    }
                }

                $parentCode = Code::where('CODE', $key)->first();
                if($parentCode->codeCategory->name=='PENDAPATAN')
                {
                    $pendapatan->push((object)[
                        'code' => $parentCode,
                        'saldo' => $saldo
                    ]);
                }
            }

            $data['pendapatanByJenis'] = $pendapatan;
        }

        return view('pendapatan.laporan', $data);
    }
}
