<?php

namespace App\Http\Controllers;

use App\Exports\PendapatanExport;
use App\Exports\PendapatanExportPDF;
use App\Models\Code;
use App\Models\CodeCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
            // $groupLabaRugi = CodeCategory::where('name', 'like', 'LABA%')
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

            $saldoMonthGroup = collect([
                '1' => 0,
                '2' => 0,
                '3' => 0,
                '4' => 0,
                '5' => 0,
                '6' => 0,
                '7' => 0,
                '8' => 0,
                '9' => 0,
                '10' => 0,
                '11' => 0,
                '12' => 0
            ]);

            foreach ($groupCodes as $key => $groupCode)
            {
                $saldo = 0;
                $saldoMonth = collect([
                    '1' => 0,
                    '2' => 0,
                    '3' => 0,
                    '4' => 0,
                    '5' => 0,
                    '6' => 0,
                    '7' => 0,
                    '8' => 0,
                    '9' => 0,
                    '10' => 0,
                    '11' => 0,
                    '12' => 0
                ]);
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

                        // saldo per month
                        $debetMonth = DB::table('t_jurnal')
                                        ->where('akun_debet', $code->CODE)
                                        ->whereYear('created_at', $year)
                                        ->get()
                                        ->groupBy(function ($val)
                                        {
                                            return Carbon::parse($val->created_at)->month;
                                        });
                        $kreditMonth = DB::table('t_jurnal')
                                        ->where('akun_kredit', $code->CODE)
                                        ->whereYear('created_at', $year)
                                        ->get()
                                        ->groupBy(function ($val)
                                        {
                                            return Carbon::parse($val->created_at)->month;
                                        });

                        foreach ($saldoMonth as $k => $value)
                        {
                            $debet = (isset($debetMonth[$k]))? $debetMonth[$k]->sum('debet'):0;
                            $kredit = (isset($kreditMonth[$k]))? $kreditMonth[$k]->sum('kredit'):0;
                            $saldoMonth[$k] = $saldoMonth[$k] + $debet - $kredit;
                            $saldoMonthGroup[$k] = $saldoMonthGroup[$k] + $debet - $kredit;
                        }
                    }
                    else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                    {
                        // period's month
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereYear('created_at', $year)->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereYear('created_at', $year)->sum('kredit');

                        $saldo -= $saldoDebet;
                        $saldo += $saldoKredit;

                        // saldo per month
                        $debetMonth = DB::table('t_jurnal')
                                        ->where('akun_debet', $code->CODE)
                                        ->whereYear('created_at', $year)
                                        ->get()
                                        ->groupBy(function ($val)
                                        {
                                            return Carbon::parse($val->created_at)->month;
                                        });
                        $kreditMonth = DB::table('t_jurnal')
                                        ->where('akun_kredit', $code->CODE)
                                        ->whereYear('created_at', $year)
                                        ->get()
                                        ->groupBy(function ($val)
                                        {
                                            return Carbon::parse($val->created_at)->month;
                                        });

                        foreach ($saldoMonth as $k => $value)
                        {
                            $debet = (isset($debetMonth[$k]))? $debetMonth[$k]->sum('debet'):0;
                            $kredit = (isset($kreditMonth[$k]))? $kreditMonth[$k]->sum('kredit'):0;
                            $saldoMonth[$k] = $saldoMonth[$k] - $debet + $kredit;
                            $saldoMonthGroup[$k] = $saldoMonthGroup[$k] - $debet + $kredit;
                        }
                    }
                }

                $parentCode = Code::where('CODE', $key)->first();

                if($parentCode->codeCategory->name=='PENDAPATAN')
                // if($parentCode->codeCategory->name=='LABA/RUGI')
                {
                    $pendapatan->push((object)[
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoMonth' => $saldoMonth
                    ]);
                }
            }

            $data['pendapatanByJenis'] = $pendapatan;
            $data['saldoMonthGroup'] = $saldoMonthGroup;
        }

        return view('pendapatan.laporan', $data);
    }

    public function downloadExcel(Request $request)
    {
        $year = $request->year;
        // $year = 2021;
        $data['request'] = $request;
        if ($year)
        {
            $groupLabaRugi = CodeCategory::where('name', 'like', 'PENDAPATAN%')
            // $groupLabaRugi = CodeCategory::where('name', 'like', 'LABA%')
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

            $saldoMonthGroup = collect([
                '1' => 0,
                '2' => 0,
                '3' => 0,
                '4' => 0,
                '5' => 0,
                '6' => 0,
                '7' => 0,
                '8' => 0,
                '9' => 0,
                '10' => 0,
                '11' => 0,
                '12' => 0
            ]);

            foreach ($groupCodes as $key => $groupCode)
            {
                $saldo = 0;
                $saldoMonth = collect([
                    '1' => 0,
                    '2' => 0,
                    '3' => 0,
                    '4' => 0,
                    '5' => 0,
                    '6' => 0,
                    '7' => 0,
                    '8' => 0,
                    '9' => 0,
                    '10' => 0,
                    '11' => 0,
                    '12' => 0
                ]);
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

                        // saldo per month
                        $debetMonth = DB::table('t_jurnal')
                                        ->where('akun_debet', $code->CODE)
                                        ->whereYear('created_at', $year)
                                        ->get()
                                        ->groupBy(function ($val)
                                        {
                                            return Carbon::parse($val->created_at)->month;
                                        });
                        $kreditMonth = DB::table('t_jurnal')
                                        ->where('akun_kredit', $code->CODE)
                                        ->whereYear('created_at', $year)
                                        ->get()
                                        ->groupBy(function ($val)
                                        {
                                            return Carbon::parse($val->created_at)->month;
                                        });

                        foreach ($saldoMonth as $k => $value)
                        {
                            $debet = (isset($debetMonth[$k]))? $debetMonth[$k]->sum('debet'):0;
                            $kredit = (isset($kreditMonth[$k]))? $kreditMonth[$k]->sum('kredit'):0;
                            $saldoMonth[$k] = $saldoMonth[$k] + $debet - $kredit;
                            $saldoMonthGroup[$k] = $saldoMonthGroup[$k] + $debet - $kredit;
                        }
                    }
                    else if($code->normal_balance_id == NORMAL_BALANCE_KREDIT)
                    {
                        // period's month
                        $saldoDebet = DB::table('t_jurnal')->where('akun_debet', $code->CODE)->whereYear('created_at', $year)->sum('debet');
                        $saldoKredit = DB::table('t_jurnal')->where('akun_kredit', $code->CODE)->whereYear('created_at', $year)->sum('kredit');

                        $saldo -= $saldoDebet;
                        $saldo += $saldoKredit;

                        // saldo per month
                        $debetMonth = DB::table('t_jurnal')
                                        ->where('akun_debet', $code->CODE)
                                        ->whereYear('created_at', $year)
                                        ->get()
                                        ->groupBy(function ($val)
                                        {
                                            return Carbon::parse($val->created_at)->month;
                                        });
                        $kreditMonth = DB::table('t_jurnal')
                                        ->where('akun_kredit', $code->CODE)
                                        ->whereYear('created_at', $year)
                                        ->get()
                                        ->groupBy(function ($val)
                                        {
                                            return Carbon::parse($val->created_at)->month;
                                        });

                        foreach ($saldoMonth as $k => $value)
                        {
                            $debet = (isset($debetMonth[$k]))? $debetMonth[$k]->sum('debet'):0;
                            $kredit = (isset($kreditMonth[$k]))? $kreditMonth[$k]->sum('kredit'):0;
                            $saldoMonth[$k] = $saldoMonth[$k] - $debet + $kredit;
                            $saldoMonthGroup[$k] = $saldoMonthGroup[$k] - $debet + $kredit;
                        }
                    }
                }

                $parentCode = Code::where('CODE', $key)->first();

                if($parentCode->codeCategory->name=='PENDAPATAN')
                // if($parentCode->codeCategory->name=='LABA/RUGI')
                {
                    $pendapatan->push((object)[
                        'code' => $parentCode,
                        'saldo' => $saldo,
                        'saldoMonth' => $saldoMonth
                    ]);
                }
            }

            $data['pendapatanByJenis'] = $pendapatan;
            $data['saldoMonthGroup'] = $saldoMonthGroup;

            if($request->pdf)
            {
                $filename = 'pendapatan-'.Carbon::now()->format('Y').'.pdf';
                return Excel::download(new PendapatanExportPDF($data), $filename);
            }
            $filename = 'pendapatan-'.Carbon::now()->format('Y').'.xlsx';
            return Excel::download(new PendapatanExport($data), $filename);
        }

        return redirect()->back()->withErrors('Terjadi Kesalahan');
    }
}
