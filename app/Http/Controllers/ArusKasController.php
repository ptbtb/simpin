<?php

namespace App\Http\Controllers;

use App\Exports\ArusKasExport;
use App\Models\Jurnal;
use App\Models\Code;
use App\Models\CodeCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ArusKasController extends Controller
{
    public function laporan(Request $request)
    {
        $data['title'] = 'Laporan Arus Kas';
        $data['request'] = $request;
        // check if from and to date has been selected
        if(!$request->from)
        {          
            $request->from = Carbon::today()->startOfMonth()->format('d-m-Y');
        }
        if(!$request->to)
        {          
            $request->to = Carbon::today()->endOfMonth()->format('d-m-Y');
        }

        if ($request->search)
        {

            if ($request->from && $request->to)
            {
                // find kas and bank account
                $kasAndBankAccount = Code::where('CODE', 'like','102.%')->orWhere('CODE', 'like','101.%')->get();
                $kasAndBankAccount = $kasAndBankAccount->where('is_parent', 0)->pluck('CODE');

                // count saldo awal
                $startSaldoAwalPeriod = Carbon::createFromFormat('d-m-Y', '01-01-2020')->format('Y-m-d');
                $endSaldoAwalPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->subDays()->format('Y-m-d');

                $saldoAwalKredit = DB::table('t_jurnal')->whereIn('akun_kredit', $kasAndBankAccount)->whereBetween('tgl_transaksi', [$startSaldoAwalPeriod, $endSaldoAwalPeriod])->get()->sum('kredit');
                $saldoAwalDebet = DB::table('t_jurnal')->whereIn('akun_debet', $kasAndBankAccount)->whereBetween('tgl_transaksi', [$startSaldoAwalPeriod, $endSaldoAwalPeriod])->get()->sum('debet');

                $totalSaldoAwal = $saldoAwalDebet - $saldoAwalKredit;
                $data['saldoAwal'] = $totalSaldoAwal;

                // get start/end period (period's month)
                $startPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->format('Y-m-d');
                $endPeriod = Carbon::createFromFormat('d-m-Y', $request->to)->format('Y-m-d');

                // store data to view
                $data['startPeriod'] = Carbon::createFromFormat('d-m-Y', $request->from)->format('Y-m-d');
                $data['endPeriod'] = Carbon::createFromFormat('d-m-Y', $request->to)->format('Y-m-d');

                // get all transaction in period
                $allTransactions = DB::table('t_jurnal')->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->get();

                // get all jurnal_temp transaction in period
                $jurnalTempTransactions = DB::table('jurnal_temp')->whereBetween('periode', [$startPeriod, $endPeriod])->get();

                // cari akun kas atau bank di jurnal selama period, unique
                // bedakan antara debet dan kredit
                $groupKasAndBankKredit = DB::table('t_jurnal')->whereIn('akun_kredit', $kasAndBankAccount)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->get()->groupBy('jurnalable_id', 'jurnalable_type');
                
                $groupKasAndBankDebet = DB::table('t_jurnal')->whereIn('akun_debet', $kasAndBankAccount)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->get()->groupBy('jurnalable_id', 'jurnalable_type');

                // cari transaksi yang ada kas dan bank
                $kreditTransactions = $groupKasAndBankKredit->map(function ($item, $key) {
                                                return $item->first()->jurnalable_type;
                                            });

                $debetTransactions = $groupKasAndBankDebet->map(function ($item, $key) {
                                                return $item->first()->jurnalable_type;
                                            });

                $pengeluarans = array();
                $penerimaans = array();

                // cari lawan nya di Table Jurnal, karena bisa jadi lawan nya beda row
                foreach ($kreditTransactions as $key => $kreditTransaction) 
                {
                    if($key != '')
                    {
                        if($kreditTransaction == 'App\Models\JurnalTemp')
                        {
                            $serialNumber = optional($jurnalTempTransactions->where('id', $key)->first())->serial_number;
                            $jurnalTemps = $jurnalTempTransactions->where('serial_number', $serialNumber)->pluck('id');
                            
                            $temps = $allTransactions->whereIn('jurnalable_id', $jurnalTemps)
                                                    ->where('jurnalable_type', $kreditTransaction)
                                                    ->where('akun_debet', '<>', 0)
                                                    ->pluck('debet','akun_debet')
                                                    ->all();
                        }
                        else
                        {
                            $temps = $allTransactions->where('jurnalable_id', $key)
                                                    ->where('jurnalable_type', $kreditTransaction)
                                                    ->where('akun_debet', '<>', 0)
                                                    ->pluck('debet','akun_debet')
                                                    ->all();
                        }

                        foreach ($temps as $key => $temp) 
                        {
                            $subKey = substr($key, 0, 6);
                            
                            if(array_key_exists($subKey, $pengeluarans))
                            {
                                $pengeluarans[$subKey] += $temp;
                            }
                            else
                            {
                                $pengeluarans[$subKey] = (int)$temp;
                            }
                        }
                    }
                }
                
                foreach ($debetTransactions as $key => $debetTransaction) 
                {
                    if($key != '')
                    {
                        if($debetTransaction == 'App\Models\JurnalTemp')
                        {
                            $serialNumber = optional($jurnalTempTransactions->where('id', $key)->first())->serial_number;
                            $jurnalTemps = $jurnalTempTransactions->where('serial_number', $serialNumber)->pluck('id');
                            
                            $temps = $allTransactions->whereIn('jurnalable_id', $jurnalTemps)
                                                    ->where('jurnalable_type', $debetTransaction)
                                                    ->where('akun_kredit', '<>', 0)
                                                    ->pluck('debet','akun_kredit')
                                                    ->all();
                        }
                        else
                        {
                            $temps = $allTransactions->where('jurnalable_id', $key)
                                                    ->where('jurnalable_type', $debetTransaction)
                                                    ->where('akun_kredit', '<>', 0)
                                                    ->pluck('kredit','akun_kredit')
                                                    ->all();
                        }

                        foreach ($temps as $key => $temp) 
                        {
                            $subKey = substr($key, 0, 6);

                            if(array_key_exists($subKey, $penerimaans))
                            {
                                $penerimaans[$subKey] += $temp;
                            }
                            else
                            {
                                $penerimaans[$subKey] = (int)$temp;
                            }
                        }
                    }
                }

                $dataPengeluaran = collect();
                $dataPenerimaan = collect();
                $totalPengeluaran = 0;
                $totalPenerimaan = 0;

                // get uraian for pengeluaran and penerimaan
                foreach($pengeluarans as $key => $pengeluaran)
                {
                    $code = Code::where('CODE', $key . '.000')->first();

                    if($code)
                    {
                        $dataPengeluaran->push(['code' => $key, 'uraian' => $code->NAMA_TRANSAKSI, 'nominal' => $pengeluaran]);
                    }
                    else
                    {
                        $dataPengeluaran->push(['code' => $key, 'uraian' => '-', 'nominal' => $pengeluaran]);
                    }

                    // total
                    $totalPengeluaran += $pengeluaran;
                }

                foreach($penerimaans as $key => $penerimaan)
                {
                    $code = Code::where('CODE', $key . '.000')->first();

                    if($code)
                    {
                        $dataPenerimaan->push(['code' => $key, 'uraian' => $code->NAMA_TRANSAKSI, 'nominal' => $penerimaan]);
                    }
                    else
                    {
                        $dataPenerimaan->push(['code' => $key, 'uraian' => '-', 'nominal' => $penerimaan]);
                    }

                    // total
                    $totalPenerimaan += $penerimaan;
                }
                
                $data['dataPengeluaran'] = $dataPengeluaran;
                $data['dataPenerimaan'] = $dataPenerimaan;
                $data['totalPengeluaran'] = $totalPengeluaran;
                $data['totalPenerimaan'] = $totalPenerimaan;
                $data['saldoAkhir'] = $totalSaldoAwal + ($totalPenerimaan - $totalPengeluaran);
            }
        }

        return view('arus_kas.laporan', $data);
    }

    public function downloadExcel(Request $request)
    {
        $data['request'] = $request;

        // check if period date has been selected
        if(!$request->period)
        {
            $request->period = Carbon::today()->format('m-Y');
        }

        if ($request->period)
        {
            // find kas and bank account
            $kasAndBankAccount = Code::where('CODE', 'like','102.%')->orWhere('CODE', 'like','101.%')->get();
            $kasAndBankAccount = $kasAndBankAccount->where('is_parent', 0)->pluck('CODE');

            // count saldo awal
            $startSaldoAwalPeriod = Carbon::createFromFormat('d-m-Y', '01-01-2020')->format('Y-m-d');
            $endSaldoAwalPeriod = Carbon::createFromFormat('m-Y', $request->period)->startOfMonth()->subDays()->format('Y-m-d');

            $saldoAwalKredit = DB::table('t_jurnal')->whereIn('akun_kredit', $kasAndBankAccount)->whereBetween('tgl_transaksi', [$startSaldoAwalPeriod, $endSaldoAwalPeriod])->get()->sum('kredit');
            $saldoAwalDebet = DB::table('t_jurnal')->whereIn('akun_debet', $kasAndBankAccount)->whereBetween('tgl_transaksi', [$startSaldoAwalPeriod, $endSaldoAwalPeriod])->get()->sum('debet');

            $totalSaldoAwal = $saldoAwalDebet - $saldoAwalKredit;
            $data['saldoAwal'] = $totalSaldoAwal;

            // get start/end period (period's month)
            $startPeriod = Carbon::createFromFormat('m-Y', $request->period)->startOfMonth()->format('Y-m-d');
            $endPeriod = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('Y-m-d');

            // store data to view
            $data['startPeriod'] = Carbon::createFromFormat('m-Y', $request->period)->startOfMonth()->format('d-m-Y');
            $data['endPeriod'] = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('d-m-Y');

            // get all transaction in period
            $allTransactions = DB::table('t_jurnal')->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->get();

            // get all jurnal_temp transaction in period
            $jurnalTempTransactions = DB::table('jurnal_temp')->whereBetween('periode', [$startPeriod, $endPeriod])->get();
            
            // cari akun kas atau bank di jurnal selama period, unique
            // bedakan antara debet dan kredit
            $groupKasAndBankKredit = DB::table('t_jurnal')->whereIn('akun_kredit', $kasAndBankAccount)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->get()->groupBy('jurnalable_id', 'jurnalable_type');
            
            $groupKasAndBankDebet = DB::table('t_jurnal')->whereIn('akun_debet', $kasAndBankAccount)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->get()->groupBy('jurnalable_id', 'jurnalable_type');

            // cari transaksi yang ada kas dan bank
            $kreditTransactions = $groupKasAndBankKredit->map(function ($item, $key) {
                                            return $item->first()->jurnalable_type;
                                        });

            $debetTransactions = $groupKasAndBankDebet->map(function ($item, $key) {
                                            return $item->first()->jurnalable_type;
                                        });

            $pengeluarans = array();
            $penerimaans = array();

            // cari lawan nya di Table Jurnal, karena bisa jadi lawan nya beda row
            foreach ($kreditTransactions as $key => $kreditTransaction) 
            {
                if($key != '')
                {
                    if($kreditTransaction == 'App\Models\JurnalTemp')
                    {
                        $serialNumber = optional($jurnalTempTransactions->where('id', $key)->first())->serial_number;
                        $jurnalTemps = $jurnalTempTransactions->where('serial_number', $serialNumber)->pluck('id');
                        
                        $temps = $allTransactions->whereIn('jurnalable_id', $jurnalTemps)
                                                ->where('jurnalable_type', $kreditTransaction)
                                                ->where('akun_debet', '<>', 0)
                                                ->pluck('debet','akun_debet')
                                                ->all();
                    }
                    else
                    {
                        $temps = $allTransactions->where('jurnalable_id', $key)
                                                ->where('jurnalable_type', $kreditTransaction)
                                                ->where('akun_debet', '<>', 0)
                                                ->pluck('debet','akun_debet')
                                                ->all();
                    }

                    foreach ($temps as $key => $temp) 
                    {
                        $subKey = substr($key, 0, 6);
                        
                        if(array_key_exists($subKey, $pengeluarans))
                        {
                            $pengeluarans[$subKey] += $temp;
                        }
                        else
                        {
                            $pengeluarans[$subKey] = (int)$temp;
                        }
                    }
                }
            }
            
            foreach ($debetTransactions as $key => $debetTransaction) 
            {
                if($key != '')
                {
                    if($debetTransaction == 'App\Models\JurnalTemp')
                    {
                        $serialNumber = optional($jurnalTempTransactions->where('id', $key)->first())->serial_number;
                        $jurnalTemps = $jurnalTempTransactions->where('serial_number', $serialNumber)->pluck('id');
                        
                        $temps = $allTransactions->whereIn('jurnalable_id', $jurnalTemps)
                                                ->where('jurnalable_type', $debetTransaction)
                                                ->where('akun_kredit', '<>', 0)
                                                ->pluck('debet','akun_kredit')
                                                ->all();
                    }
                    else
                    {
                        $temps = $allTransactions->where('jurnalable_id', $key)
                                                ->where('jurnalable_type', $debetTransaction)
                                                ->where('akun_kredit', '<>', 0)
                                                ->pluck('kredit','akun_kredit')
                                                ->all();
                    }

                    foreach ($temps as $key => $temp) 
                    {
                        $subKey = substr($key, 0, 6);

                        if(array_key_exists($subKey, $penerimaans))
                        {
                            $penerimaans[$subKey] += $temp;
                        }
                        else
                        {
                            $penerimaans[$subKey] = (int)$temp;
                        }
                    }
                }
            }

            $dataPengeluaran = collect();
            $dataPenerimaan = collect();
            $totalPengeluaran = 0;
            $totalPenerimaan = 0;

            // get uraian for pengeluaran and penerimaan
            foreach($pengeluarans as $key => $pengeluaran)
            {
                $code = Code::where('CODE', $key . '.000')->first();

                if($code)
                {
                    $dataPengeluaran->push(['code' => $key, 'uraian' => $code->NAMA_TRANSAKSI, 'nominal' => $pengeluaran]);
                }
                else
                {
                    $dataPengeluaran->push(['code' => $key, 'uraian' => '-', 'nominal' => $pengeluaran]);
                }

                // total
                $totalPengeluaran += $pengeluaran;
            }

            foreach($penerimaans as $key => $penerimaan)
            {
                $code = Code::where('CODE', $key . '.000')->first();

                if($code)
                {
                    $dataPenerimaan->push(['code' => $key, 'uraian' => $code->NAMA_TRANSAKSI, 'nominal' => $penerimaan]);
                }
                else
                {
                    $dataPenerimaan->push(['code' => $key, 'uraian' => '-', 'nominal' => $penerimaan]);
                }

                // total
                $totalPenerimaan += $penerimaan;
            }
            
            $data['dataPengeluaran'] = $dataPengeluaran;
            $data['dataPenerimaan'] = $dataPenerimaan;
            $data['totalPengeluaran'] = $totalPengeluaran;
            $data['totalPenerimaan'] = $totalPenerimaan;
            $data['saldoAkhir'] = $totalSaldoAwal + ($totalPenerimaan - $totalPengeluaran);

            $filename = 'arus-kas-'.$request->period.'.xlsx';
            return Excel::download(new ArusKasExport($data), $filename);
        }

        return redirect()->back()->withErrors('Terjadi Kesalahan');
    }
}
