<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Code;
use App\Models\CodeCategory;
use Illuminate\Http\Request;
use App\Exports\NeracaExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\LabaRugiController;

use Rap2hpoutre\FastExcel\FastExcel;

use Carbon\Carbon;
use Excel;
use DB;
use Illuminate\Support\Facades\Cache;
use PDF;

class NeracaController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try {
            // period
            // check if period date has been selected
            if (!$request->period) {
                $request->period = Carbon::today()->format('m-Y');
            }
            if ($request->search) {
                $data = Cache::remember('laporan_neraca_'.$request->period, DB_QUERY_CACHE_PERIOD_MEDIUM, function () use ($request) {
                    $shu = (new LabaRugiController())->getSHU($request);

                    $groupNeraca = CodeCategory::where('name', 'like', 'AKTIVA%')
                    ->orWhere('name', 'like', 'KEWAJIBAN%')
                    ->orWhere('name', 'like', 'KEKAYAAN%')
                    ->get();

                    $codes = Code::
                                        // where('is_parent', 0)
                    where(function ($query) use ($groupNeraca) {
                        for ($i = 0; $i < count($groupNeraca); $i++) {
                            $query->orWhere('code_category_id', $groupNeraca[$i]->id);
                        }
                    })
                                    //->whereIn('code_type_id', [CODE_TYPE_ACTIVA, CODE_TYPE_PASSIVA])
                    ->get();

                    // aktiva collection
                    $aktivalancar = collect();
                    $aktivatetap = collect();
                    $kewajibanlancar = collect();
                    $kewajibanjangkapanjang = collect();
                    $kekayaanbersih = collect();

                    $groupCodes = $codes->groupBy(function ($item, $key) {
                        return substr($item['CODE'], 0, 3);
                    });

                    // create compare period, sub month from selected period
                    $request->compare_period = Carbon::createFromFormat('m-Y', $request->period)->subMonth()->format('m-Y');

                    // get start/end period and sub period
                    $startPeriod = Carbon::createFromFormat('Y-m-d', '2020-12-31')->format('Y-m-d');
                    $endPeriod = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('Y-m-d');

                    $startComparePeriod = Carbon::createFromFormat('Y-m-d', '2020-11-30')->format('Y-m-d');
                    $endComparePeriod = Carbon::createFromFormat('m-Y', $request->compare_period)->endOfMonth()->format('Y-m-d');

                    foreach ($groupCodes as $key => $groupCode) {
                        $saldo = 0;
                        $saldoLastMonth = 0;
                        foreach ($groupCode as $key1 => $code) {
                            // get code's normal balance
                            if ($code->normal_balance_id == NORMAL_BALANCE_DEBET) {
                                $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');
                                $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');

                                $saldo += $saldoDebet;
                                $saldo -= $saldoKredit;

                                $saldoDebetLastMonth = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                                $saldoKreditLastMonth = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');

                                $saldoLastMonth += $saldoDebetLastMonth;
                                $saldoLastMonth -= $saldoKreditLastMonth;
                            } elseif ($code->normal_balance_id == NORMAL_BALANCE_KREDIT) {
                                $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');

                                $parcode = Code::find($code->id);

                                if ($parcode->codeCategory->name=='KEWAJIBAN LANCAR' &&  $parcode->codeType->name=='Passiva') {
                                    $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                                    $saldo += $saldoDebet;
                                    $saldo -= $saldoKredit;
                                } elseif ($parcode->codeCategory->name=='AKTIVA TETAP' &&  $parcode->codeType->name=='Activa') {
                                    $saldoKreditJurnalUmum = Jurnal::where('akun_kredit', $code->CODE)->whereIn('jurnalable_type', ['App\Models\JurnalUmum','App\Models\JurnalTemp'])->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                                    $saldoKreditSaldoAwal = Jurnal::where('akun_kredit', $code->CODE)->where('jurnalable_type', 'App\Models\SaldoAwal')->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                                    $saldoKredit = $saldoKreditSaldoAwal + (-1 * $saldoKreditJurnalUmum);

                                    $saldo += $saldoDebet;
                                    $saldo -= $saldoKredit;
                                } else {
                                    $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                                    $saldo -= $saldoDebet;
                                    $saldo += $saldoKredit;
                                }



                                $saldoDebetLastMonth = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                                if ($parcode->codeCategory->name=='KEWAJIBAN LANCAR' &&  $parcode->codeType->name=='Passiva') {
                                    $saldoKreditLastMonth = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                                    $saldoLastMonth += $saldoDebetLastMonth;
                                    $saldoLastMonth -= $saldoKreditLastMonth;
                                } elseif ($parcode->codeCategory->name=='AKTIVA TETAP' &&  $parcode->codeType->name=='Activa') {
                                    $saldoKreditJurnalUmum = Jurnal::where('akun_kredit', $code->CODE)->whereIn('jurnalable_type', ['App\Models\JurnalUmum','App\Models\JurnalTemp'])->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                                    $saldoKreditSaldoAwal = Jurnal::where('akun_kredit', $code->CODE)->where('jurnalable_type', 'App\Models\SaldoAwal')->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                                    $saldoKreditLastMonth = $saldoKreditSaldoAwal + (-1 * $saldoKreditJurnalUmum);

                                    $saldoLastMonth += $saldoDebetLastMonth;
                                    $saldoLastMonth -= $saldoKreditLastMonth;
                                } else {
                                    $saldoKreditLastMonth = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                                    $saldoLastMonth -= $saldoDebetLastMonth;
                                    $saldoLastMonth += $saldoKreditLastMonth;
                                }
                            }
                        }

                        if ($key==965 || $key==974 || $key==975 || $key==607) {
                            $parentCode = Code::where('CODE', 'like', ''.$key . '%')->first();
                        } else {
                            $parentCode = Code::where('CODE', $key . '.00.000')->first();
                        }

                        if ($parentCode->codeCategory->name=='AKTIVA LANCAR') {
                            $aktivalancar->push([
                                'code' => $parentCode,
                                'saldo' => $saldo,
                                'saldoLastMonth' => $saldoLastMonth,
                            ]);
                        } elseif ($parentCode->codeCategory->name=='AKTIVA TETAP') {
                            if ($parentCode->codeType->name=='Activa' && $parentCode->normal_balance_id==NORMAL_BALANCE_KREDIT) {
                                $aktivatetap->push([
                                    'code' => $parentCode,
                                    'saldo' => -1*$saldo,
                                    'saldoLastMonth' => -1*$saldoLastMonth,
                                ]);
                            } else {
                                $aktivatetap->push([
                                'code' => $parentCode,
                                'saldo' => $saldo,
                                'saldoLastMonth' => $saldoLastMonth,
                            ]);
                            }
                        } elseif ($parentCode->codeCategory->name=='KEWAJIBAN LANCAR') {
                            if ($parentCode->codeType->name=='Passiva' && $parentCode->normal_balance_id==NORMAL_BALANCE_KREDIT) {
                                $kewajibanlancar->push([
                                'code' => $parentCode,
                                'saldo' => -1*$saldo,
                                'saldoLastMonth' => -1*$saldoLastMonth,
                            ]);
                            } else {
                                $kewajibanlancar->push([
                                'code' => $parentCode,
                                'saldo' => $saldo,
                                'saldoLastMonth' => $saldoLastMonth,
                            ]);
                            }
                        } elseif ($parentCode->codeCategory->name=='KEWAJIBAN JANGKA PANJANG') {
                            $kewajibanjangkapanjang->push([
                            'code' => $parentCode,
                            'saldo' => $saldo,
                            'saldoLastMonth' => $saldoLastMonth,
                        ]);
                        } elseif ($parentCode->codeCategory->name=='KEKAYAAN BERSIH') {
                            if ($key==607) {
                                $kekayaanbersih->push([
                                'code' => $parentCode,
                                'saldo' => $saldo + $shu[0],
                                'saldoLastMonth' => $saldoLastMonth+$shu[1],
                            ]);
                            } else {
                                $kekayaanbersih->push([
                                'code' => $parentCode,
                                'saldo' => $saldo,
                                'saldoLastMonth' => $saldoLastMonth,
                            ]);
                            }
                        }
                    }

                    $aktivatetap = $aktivatetap->sortBy('code');
                    $aktivalancar = $aktivalancar->sortBy('code');
                    $kewajibanlancar = $kewajibanlancar->sortBy('code');
                    $kewajibanjangkapanjang = $kewajibanjangkapanjang->sortBy('code');
                    $kekayaanbersih = $kekayaanbersih->sortBy('code');


                    $data['aktivatetap'] = $aktivatetap;
                    $data['aktivalancar'] = $aktivalancar;
                    $data['kewajibanlancar'] = $kewajibanlancar;
                    $data['kewajibanjangkapanjang'] = $kewajibanjangkapanjang;
                    $data['kekayaanbersih'] = $kekayaanbersih;

                    return $data;
                });
            }

            $data['title'] = 'Laporan Neraca';
            $data['request'] = $request;

            return view('neraca.index', $data);
        } catch (\Throwable $e) {
            $message = class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function createExcel(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try {
          $shu = (new LabaRugiController())->getSHU($request);

          $groupNeraca = CodeCategory::where('name', 'like', 'AKTIVA%')
          ->orWhere('name', 'like', 'KEWAJIBAN%')
          ->orWhere('name', 'like', 'KEKAYAAN%')
          ->get();

          $codes = Code::
                              // where('is_parent', 0)
          where(function ($query) use ($groupNeraca) {
              for ($i = 0; $i < count($groupNeraca); $i++) {
                  $query->orWhere('code_category_id', $groupNeraca[$i]->id);
              }
          })
                          //->whereIn('code_type_id', [CODE_TYPE_ACTIVA, CODE_TYPE_PASSIVA])
          ->get();

          // aktiva collection
          $aktivalancar = collect();
          $aktivatetap = collect();
          $kewajibanlancar = collect();
          $kewajibanjangkapanjang = collect();
          $kekayaanbersih = collect();

          $groupCodes = $codes->groupBy(function ($item, $key) {
              return substr($item['CODE'], 0, 3);
          });

          // create compare period, sub month from selected period
          $request->compare_period = Carbon::createFromFormat('m-Y', $request->period)->subMonth()->format('m-Y');

          // get start/end period and sub period
          $startPeriod = Carbon::createFromFormat('Y-m-d', '2020-12-31')->format('Y-m-d');
          $endPeriod = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('Y-m-d');

          $startComparePeriod = Carbon::createFromFormat('Y-m-d', '2020-11-30')->format('Y-m-d');
          $endComparePeriod = Carbon::createFromFormat('m-Y', $request->compare_period)->endOfMonth()->format('Y-m-d');

          foreach ($groupCodes as $key => $groupCode) {
              $saldo = 0;
              $saldoLastMonth = 0;
              foreach ($groupCode as $key1 => $code) {
                  // get code's normal balance
                  if ($code->normal_balance_id == NORMAL_BALANCE_DEBET) {
                      $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');
                      $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');

                      $saldo += $saldoDebet;
                      $saldo -= $saldoKredit;

                      $saldoDebetLastMonth = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                      $saldoKreditLastMonth = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');

                      $saldoLastMonth += $saldoDebetLastMonth;
                      $saldoLastMonth -= $saldoKreditLastMonth;
                  } elseif ($code->normal_balance_id == NORMAL_BALANCE_KREDIT) {
                      $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');

                      $parcode = Code::find($code->id);

                      if ($parcode->codeCategory->name=='KEWAJIBAN LANCAR' &&  $parcode->codeType->name=='Passiva') {
                          $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                          $saldo += $saldoDebet;
                          $saldo -= $saldoKredit;
                      } elseif ($parcode->codeCategory->name=='AKTIVA TETAP' &&  $parcode->codeType->name=='Activa') {
                          $saldoKreditJurnalUmum = Jurnal::where('akun_kredit', $code->CODE)->whereIn('jurnalable_type', ['App\Models\JurnalUmum','App\Models\JurnalTemp'])->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                          $saldoKreditSaldoAwal = Jurnal::where('akun_kredit', $code->CODE)->where('jurnalable_type', 'App\Models\SaldoAwal')->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                          $saldoKredit = $saldoKreditSaldoAwal + (-1 * $saldoKreditJurnalUmum);

                          $saldo += $saldoDebet;
                          $saldo -= $saldoKredit;
                      } else {
                          $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                          $saldo -= $saldoDebet;
                          $saldo += $saldoKredit;
                      }



                      $saldoDebetLastMonth = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                      if ($parcode->codeCategory->name=='KEWAJIBAN LANCAR' &&  $parcode->codeType->name=='Passiva') {
                          $saldoKreditLastMonth = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                          $saldoLastMonth += $saldoDebetLastMonth;
                          $saldoLastMonth -= $saldoKreditLastMonth;
                      } elseif ($parcode->codeCategory->name=='AKTIVA TETAP' &&  $parcode->codeType->name=='Activa') {
                          $saldoKreditJurnalUmum = Jurnal::where('akun_kredit', $code->CODE)->whereIn('jurnalable_type', ['App\Models\JurnalUmum','App\Models\JurnalTemp'])->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                          $saldoKreditSaldoAwal = Jurnal::where('akun_kredit', $code->CODE)->where('jurnalable_type', 'App\Models\SaldoAwal')->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                          $saldoKreditLastMonth = $saldoKreditSaldoAwal + (-1 * $saldoKreditJurnalUmum);

                          $saldoLastMonth += $saldoDebetLastMonth;
                          $saldoLastMonth -= $saldoKreditLastMonth;
                      } else {
                          $saldoKreditLastMonth = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                          $saldoLastMonth -= $saldoDebetLastMonth;
                          $saldoLastMonth += $saldoKreditLastMonth;
                      }
                  }
              }

              if ($key==965 || $key==974 || $key==975 || $key==607) {
                  $parentCode = Code::where('CODE', 'like', ''.$key . '%')->first();
              } else {
                  $parentCode = Code::where('CODE', $key . '.00.000')->first();
              }

              if ($parentCode->codeCategory->name=='AKTIVA LANCAR') {
                  $aktivalancar->push([
                      'code' => $parentCode,
                      'saldo' => $saldo,
                      'saldoLastMonth' => $saldoLastMonth,
                  ]);
              } elseif ($parentCode->codeCategory->name=='AKTIVA TETAP') {
                  if ($parentCode->codeType->name=='Activa' && $parentCode->normal_balance_id==NORMAL_BALANCE_KREDIT) {
                      $aktivatetap->push([
                          'code' => $parentCode,
                          'saldo' => -1*$saldo,
                          'saldoLastMonth' => -1*$saldoLastMonth,
                      ]);
                  } else {
                      $aktivatetap->push([
                      'code' => $parentCode,
                      'saldo' => $saldo,
                      'saldoLastMonth' => $saldoLastMonth,
                  ]);
                  }
              } elseif ($parentCode->codeCategory->name=='KEWAJIBAN LANCAR') {
                  if ($parentCode->codeType->name=='Passiva' && $parentCode->normal_balance_id==NORMAL_BALANCE_KREDIT) {
                      $kewajibanlancar->push([
                      'code' => $parentCode,
                      'saldo' => -1*$saldo,
                      'saldoLastMonth' => -1*$saldoLastMonth,
                  ]);
                  } else {
                      $kewajibanlancar->push([
                      'code' => $parentCode,
                      'saldo' => $saldo,
                      'saldoLastMonth' => $saldoLastMonth,
                  ]);
                  }
              } elseif ($parentCode->codeCategory->name=='KEWAJIBAN JANGKA PANJANG') {
                  $kewajibanjangkapanjang->push([
                  'code' => $parentCode,
                  'saldo' => $saldo,
                  'saldoLastMonth' => $saldoLastMonth,
              ]);
              } elseif ($parentCode->codeCategory->name=='KEKAYAAN BERSIH') {
                  if ($key==607) {
                      $kekayaanbersih->push([
                      'code' => $parentCode,
                      'saldo' => $saldo + $shu[0],
                      'saldoLastMonth' => $saldoLastMonth+$shu[1],
                  ]);
                  } else {
                      $kekayaanbersih->push([
                      'code' => $parentCode,
                      'saldo' => $saldo,
                      'saldoLastMonth' => $saldoLastMonth,
                  ]);
                  }
              }
          }

          $aktivatetap = $aktivatetap->sortBy('code');
          $aktivalancar = $aktivalancar->sortBy('code');
          $kewajibanlancar = $kewajibanlancar->sortBy('code');
          $kewajibanjangkapanjang = $kewajibanjangkapanjang->sortBy('code');
          $kekayaanbersih = $kekayaanbersih->sortBy('code');


          $data['aktivatetap'] = $aktivatetap;
          $data['aktivalancar'] = $aktivalancar;
          $data['kewajibanlancar'] = $kewajibanlancar;
          $data['kewajibanjangkapanjang'] = $kewajibanjangkapanjang;
          $data['kekayaanbersih'] = $kekayaanbersih;





            $filename = 'export_neraca_excel_' .$request->period. '_'. Carbon::now()->format('d M Y') . '.xlsx';
            return Excel::download(new NeracaExport($data), $filename);
        } catch (\Throwable $e) {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function createPdf($period)
    {
        $this->authorize('view jurnal', Auth::user());
        try {
            $shu = (new LabaRugiController())->getSHU($request);
            $groupNeraca = CodeCategory::where('name', 'like', 'AKTIVA%')
     ->orWhere('name', 'like', 'KEWAJIBAN%')
     ->orWhere('name', 'like', 'KEKAYAAN%')
     ->get();

            $codes = Code::where('is_parent', 0)
     ->where(function ($query) use ($groupNeraca) {
         for ($i = 0; $i < count($groupNeraca); $i++) {
             $query->orWhere('code_category_id', $groupNeraca[$i]->id);
         }
     })
                            //->whereIn('code_type_id', [CODE_TYPE_ACTIVA, CODE_TYPE_PASSIVA])
     ->get();

            // aktiva collection
            $aktivalancar = collect();
            $aktivatetap = collect();
            $kewajibanlancar = collect();
            $kewajibanjangkapanjang = collect();
            $kekayaanbersih = collect();

            $groupCodes = $codes->groupBy(function ($item, $key) {
                return substr($item['CODE'], 0, 3);
            });

            // period
            // check if period date has been selected
            if (!$period) {
                $period = Carbon::today()->format('m-Y');
            }

            // create compare period, sub month from selected period
            $compare_period = Carbon::createFromFormat('m-Y', $period)->subMonth()->format('m-Y');

            // get start/end period and sub period
            $startPeriod = Carbon::createFromFormat('Y-m-d', '2020-12-31')->format('Y-m-d');
            $endPeriod = Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('Y-m-d');

            $startComparePeriod = Carbon::createFromFormat('Y-m-d', '2020-11-30')->format('Y-m-d');
            $endComparePeriod = Carbon::createFromFormat('m-Y', $request->compare_period)->endOfMonth()->format('Y-m-d');

            foreach ($groupCodes as $key => $groupCode) {
                $saldo = 0;
                $saldoLastMonth = 0;
                foreach ($groupCode as $key1 => $code) {
                    // get code's normal balance
                    if ($code->normal_balance_id == NORMAL_BALANCE_DEBET) {
                        $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');
                        $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');

                        $saldo += $saldoDebet;
                        $saldo -= $saldoKredit;

                        $saldoDebetLastMonth = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                        $saldoKreditLastMonth = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');

                        $saldoLastMonth += $saldoDebetLastMonth;
                        $saldoLastMonth -= $saldoKreditLastMonth;
                    } elseif ($code->normal_balance_id == NORMAL_BALANCE_KREDIT) {
                        $saldoDebet = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('debet');
                        $parcode = Code::find($code->id);

                        if ($parcode->codeCategory->name=='KEWAJIBAN LANCAR' &&  $parcode->codeType->name=='Passiva') {
                            $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                            $saldo += $saldoDebet;
                            $saldo -= $saldoKredit;
                        } elseif ($parcode->codeCategory->name=='AKTIVA TETAP' &&  $parcode->codeType->name=='Activa') {
                            $saldoKreditJurnalUmum = Jurnal::where('akun_kredit', $code->CODE)->whereIn('jurnalable_type', ['App\Models\JurnalUmum','App\Models\JurnalTemp'])->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                            $saldoKreditSaldoAwal = Jurnal::where('akun_kredit', $code->CODE)->where('jurnalable_type', 'App\Models\SaldoAwal')->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                            $saldoKredit = $saldoKreditSaldoAwal + (-1 * $saldoKreditJurnalUmum);

                            $saldo += $saldoDebet;
                            $saldo -= $saldoKredit;
                        } else {
                            $saldoKredit = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startPeriod, $endPeriod])->sum('kredit');
                            $saldo -= $saldoDebet;
                            $saldo += $saldoKredit;
                        }



                        $saldoDebetLastMonth = Jurnal::where('akun_debet', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('debet');
                        if ($parcode->codeCategory->name=='KEWAJIBAN LANCAR' &&  $parcode->codeType->name=='Passiva') {
                            $saldoKreditLastMonth = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                            $saldoLastMonth += $saldoDebetLastMonth;
                            $saldoLastMonth -= $saldoKreditLastMonth;
                        } elseif ($parcode->codeCategory->name=='AKTIVA TETAP' &&  $parcode->codeType->name=='Activa') {
                            $saldoKreditJurnalUmum = Jurnal::where('akun_kredit', $code->CODE)->whereIn('jurnalable_type', ['App\Models\JurnalUmum','App\Models\JurnalTemp'])->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                            $saldoKreditSaldoAwal = Jurnal::where('akun_kredit', $code->CODE)->where('jurnalable_type', 'App\Models\SaldoAwal')->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                            $saldoKreditLastMonth = $saldoKreditSaldoAwal + (-1 * $saldoKreditJurnalUmum);

                            $saldoLastMonth += $saldoDebetLastMonth;
                            $saldoLastMonth -= $saldoKreditLastMonth;
                        } else {
                            $saldoKreditLastMonth = Jurnal::where('akun_kredit', $code->CODE)->whereBetween('tgl_transaksi', [$startComparePeriod, $endComparePeriod])->sum('kredit');
                            $saldoLastMonth -= $saldoDebetLastMonth;
                            $saldoLastMonth += $saldoKreditLastMonth;
                        }
                    }
                }

                if ($key==965 || $key==974 || $key==975 || $key==607) {
                    $parentCode = Code::where('CODE', 'like', ''.$key . '%')->first();
                } else {
                    $parentCode = Code::where('CODE', $key . '.00.000')->first();
                }


                if ($parentCode->codeCategory->name=='AKTIVA LANCAR') {
                    if ($parentCode->codeType->name=='Activa' && $parentCode->normal_balance_id==NORMAL_BALANCE_KREDIT) {
                        $aktivatetap->push([
                'code' => $parentCode,
                'saldo' => -1*$saldo,
                'saldoLastMonth' => -1*$saldoLastMonth,
            ]);
                    } else {
                        $aktivatetap->push([
            'code' => $parentCode,
            'saldo' => $saldo,
            'saldoLastMonth' => $saldoLastMonth,
        ]);
                    }
                } elseif ($parentCode->codeCategory->name=='KEWAJIBAN LANCAR') {
                    if ($parentCode->codeType->name=='Passiva' && $parentCode->normal_balance_id==NORMAL_BALANCE_KREDIT) {
                        $kewajibanlancar->push([
            'code' => $parentCode,
            'saldo' => -1*$saldo,
            'saldoLastMonth' => -1*$saldoLastMonth,
        ]);
                    } else {
                        $kewajibanlancar->push([
            'code' => $parentCode,
            'saldo' => $saldo,
            'saldoLastMonth' => $saldoLastMonth,
        ]);
                    }
                } elseif ($parentCode->codeCategory->name=='KEWAJIBAN LANCAR') {
                    $kewajibanlancar->push([
        'code' => $parentCode,
        'saldo' => $saldo,
        'saldoLastMonth' => $saldoLastMonth,
    ]);
                } elseif ($parentCode->codeCategory->name=='KEWAJIBAN JANGKA PANJANG') {
                    $kewajibanjangkapanjang->push([
        'code' => $parentCode,
        'saldo' => $saldo,
        'saldoLastMonth' => $saldoLastMonth,
    ]);
                } elseif ($parentCode->codeCategory->name=='KEKAYAAN BERSIH') {
                    if ($key==607) {
                        $kekayaanbersih->push([
            'code' => $parentCode,
            'saldo' => $saldo + $shu[0],
            'saldoLastMonth' => $saldoLastMonth+$shu[1],
        ]);
                    } else {
                        $kekayaanbersih->push([
        'code' => $parentCode,
        'saldo' => $saldo,
        'saldoLastMonth' => $saldoLastMonth,
    ]);
                    }
                }
            }

            $aktivatetap = $aktivatetap->sortBy('code');
            $aktivalancar = $aktivalancar->sortBy('code');
            $kewajibanlancar = $kewajibanlancar->sortBy('code');
            $kewajibanjangkapanjang = $kewajibanjangkapanjang->sortBy('code');
            $kekayaanbersih = $kekayaanbersih->sortBy('code');


            $data['title'] = 'Laporan Neraca';
            $data['aktivatetap'] = $aktivatetap;
            $data['aktivalancar'] = $aktivalancar;
            $data['kewajibanlancar'] = $kewajibanlancar;
            $data['kewajibanjangkapanjang'] = $kewajibanjangkapanjang;
            $data['kekayaanbersih'] = $kekayaanbersih;
            $data['period'] = $period;

            view()->share('data', $data);
            PDF::setOptions(['margin-left' => 1,'margin-right' => 1, 'margin-top' => 1]);
            $pdf = PDF::loadView('neraca.createPdf', $data)->setPaper('a4', 'landscape');

            // download PDF file with download method
            $filename = 'lap-neraca-'.$period.'-'.Carbon::now()->toDateString().'.pdf';
            return $pdf->dtr($filename);
            // return view('neraca.createPdf', $data);
        } catch (\Throwable $e) {
            Log::error($e);
            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }
}
