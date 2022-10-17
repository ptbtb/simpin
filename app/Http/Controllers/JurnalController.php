<?php

namespace App\Http\Controllers;

use App\Managers\JurnalManager;
use App\Models\Jurnal;
use App\Models\TipeJurnal;
use App\Models\JurnalUmum;
use App\Models\Angsuran;
use App\Models\AngsuranPartial;
use App\Models\JurnalTemp;
use App\Models\Penarikan;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Exports\JurnalExport;
use App\Models\Anggota;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

// use Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use PDF;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view jurnal', Auth::user());
        try {
            if (!$request->from) {
                $request->from = Carbon::today()->startOfMonth()->format('d-m-Y');
            }
            if (!$request->to) {
                $request->to = Carbon::today()->endOfMonth()->format('d-m-Y');
            }

            $data['title'] = 'List Jurnal';
            $data['tipeJurnal'] = TipeJurnal::get()->pluck('name', 'id');
            $data['request'] = $request;
            return view('jurnal.index', $data);
        } catch (\Throwable $e) {
            $message = class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function indexAjax(Request $request)
    {

        try {
            $startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->startOfDay()->format('Y-m-d');
            $endUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->to)->endOfDay()->format('Y-m-d');
            $jurnal = Jurnal::with('tipeJurnal', 'createdBy')->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
            if ($request->id_tipe_jurnal) {
                $jurnal = $jurnal->where('id_tipe_jurnal', $request->id_tipe_jurnal);
            }

            if ($request->serial_number) {
                $tipeJurnal = substr($request->serial_number, 0, 3);
                $year = substr($request->serial_number, 3, 4);
                $month = substr($request->serial_number, 7, 2);
                $serialNumber = (substr($request->serial_number, 9)) ? (int)substr($request->serial_number, 9) : '';
                if ($tipeJurnal == 'ANG') {

                    $jurnalableType = 'App\Models\Angsuran';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class], function ($query) use ($year, $month, $serialNumber, $startUntilPeriod, $endUntilPeriod) {

                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);


                        });

                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                    $jurnalableType = 'App\Models\AngsuranPartial';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->orwhereHasMorph('jurnalable', [AngsuranPartial::class], function ($query) use ($year, $month, $serialNumber, $startUntilPeriod, $endUntilPeriod) {

                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);

                        });
                    } else {
                        $jurnal = $jurnal->orwhereHasMorph('jurnalable', [AngsuranPartial::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                } else if ($tipeJurnal == 'MTS') {
                    $jurnalableType = 'App\Models\JurnalTemp';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_posting', '=', $year)->whereMonth('tgl_posting', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }

                } else if ($tipeJurnal == 'TRU') {
                    $jurnalableType = 'App\Models\JurnalUmum';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {


                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                } else if ($tipeJurnal == 'TAR') {
                    $jurnalableType = 'App\Models\Penarikan';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                } else if ($tipeJurnal == 'PIJ') {
                    $jurnalableType = 'App\Models\Pinjaman';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number_kredit', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                } else if ($tipeJurnal == 'PCP') {
                    $jurnalableType = 'App\Models\Pinjaman';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_pelunasan', '=', $year)->whereMonth('tgl_pelunasan', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber,$startUntilPeriod, $endUntilPeriod) {
                            $query->whereBetween('tgl_pelunasan', [$startUntilPeriod, $endUntilPeriod]);
                        });
                    }

                } else if ($tipeJurnal == 'SIP') {
                    $jurnalableType = 'App\Models\Simpanan';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                }
            }
            if ($request->keterangan) {
                $jurnal = $jurnal->where('keterangan', 'like', '%' . $request->keterangan . '%');
            }
            if ($request->code) {
                $jurnal = $jurnal
                    ->where(function ($query) use ($request) {

                        $query->where('akun_debet', 'like', $request->code . '%')
                            ->orwhere('akun_kredit', 'like', $request->code . '%');

                    });

            }


            $jurnal = $jurnal->orderBy('tgl_transaksi', 'desc');
            return DataTables::eloquent($jurnal)->addIndexColumn()
                ->with('totaldebet', function () use ($jurnal) {
                    return $jurnal->sum('debet');
                })
                ->with('totalkredit', function () use ($jurnal) {
                    return $jurnal->sum('kredit');
                })
                ->make(true);
        } catch (\Throwable $e) {
            $message = class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            return response()->json(['message' => 'error'], 500);
        }
    }

    public function createExcel(Request $request)
    {
        try {
            if (!$request->from) {
                $request->from = Carbon::today()->startOfMonth()->format('d-m-Y');
            }
            if (!$request->to) {
                $request->to = Carbon::today()->endOfMonth()->format('d-m-Y');
            }
            $startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->format('Y-m-d');
            $endUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->to)->format('Y-m-d');
            $jurnal = Jurnal::with('tipeJurnal', 'createdBy')->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);;
            if ($request->id_tipe_jurnal) {
                $jurnal = $jurnal->where('id_tipe_jurnal', $request->id_tipe_jurnal);
            }

            if ($request->serial_number) {
                $tipeJurnal = substr($request->serial_number, 0, 3);
                $year = substr($request->serial_number, 3, 4);
                $month = substr($request->serial_number, 7, 2);
                $serialNumber = (substr($request->serial_number, 9)) ? (int)substr($request->serial_number, 9) : '';
                if ($tipeJurnal == 'ANG') {

                    $jurnalableType = 'App\Models\Angsuran';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class], function ($query) use ($year, $month, $serialNumber, $startUntilPeriod, $endUntilPeriod) {

                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                    $jurnalableType = 'App\Models\AngsuranPartial';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->orwhereHasMorph('jurnalable', [AngsuranPartial::class], function ($query) use ($year, $month, $serialNumber, $startUntilPeriod, $endUntilPeriod) {

                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->orwhereHasMorph('jurnalable', [AngsuranPartial::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                } else if ($tipeJurnal == 'MTS') {
                    $jurnalableType = 'App\Models\JurnalTemp';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_posting', '=', $year)->whereMonth('tgl_posting', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                } else if ($tipeJurnal == 'TRU') {
                    $jurnalableType = 'App\Models\JurnalUmum';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {


                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                } else if ($tipeJurnal == 'TAR') {
                    $jurnalableType = 'App\Models\Penarikan';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                } else if ($tipeJurnal == 'PIJ') {
                    $jurnalableType = 'App\Models\Pinjaman';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number_kredit', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                } else if ($tipeJurnal == 'PCP') {
                    $jurnalableType = 'App\Models\Pinjaman';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_pelunasan', '=', $year)->whereMonth('tgl_pelunasan', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber,$startUntilPeriod, $endUntilPeriod) {
                            $query->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                        });
                    }
                } else if ($tipeJurnal == 'SIP') {
                    $jurnalableType = 'App\Models\Simpanan';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                }
            }
            if ($request->code) {
                $jurnal = $jurnal
                    ->where(function ($query) use ($request) {

                        $query->where('akun_debet', 'like', $request->code . '%')
                            ->orwhere('akun_kredit', 'like', $request->code . '%');
                    });
            }


            if ($request->keterangan) {
                $jurnal = $jurnal->where('keterangan', 'like', '%' . $request->keterangan . '%');
            }

            $jurnal = $jurnal->orderBy('tgl_transaksi', 'desc')->get();
            $data['jurnal'] = $jurnal;
            return (new FastExcel($jurnal))->download('export_jurnal_excel_' . Carbon::now()->format('d M Y') . '.xlsx', function ($item) {
                return [
                    'Nomor' => $item->ser_num_view,
                    'No Anggota' => $item->kode_anggota_view,
                    'Tipe Jurnal' => ($item->tipeJurnal) ? $item->tipeJurnal->name : '',
                    'Akun Debet' => $item->akun_debet,
                    'Debet' => (float)$item->debet,
                    'Akun Kredit' => $item->akun_kredit,
                    'Kredit' => (float)$item->kredit,
                    'Keterangan' => $item->keterangan,
                    'Tanggal' => $item->tgl_transaksi,
                ];
            });
            // $filename = 'export_jurnal_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
            // return Excel::download(new JurnalExport($data), $filename);
        } catch (\Throwable $e) {
            $message = class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function createPdf(Request $request)
    {
        try {
            if (!$request->from) {
                $request->from = Carbon::today()->startOfMonth()->format('d-m-Y');
            }
            if (!$request->to) {
                $request->to = Carbon::today()->endOfMonth()->format('d-m-Y');
            }
            $startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->format('Y-m-d');
            $endUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->to)->format('Y-m-d');
            $jurnal = Jurnal::with('tipeJurnal', 'createdBy')->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);;
            if ($request->id_tipe_jurnal) {
                $jurnal = $jurnal->where('id_tipe_jurnal', $request->id_tipe_jurnal);
            }

            if ($request->serial_number) {
                $tipeJurnal = substr($request->serial_number, 0, 3);
                $year = substr($request->serial_number, 3, 4);
                $month = substr($request->serial_number, 7, 2);
                $serialNumber = (substr($request->serial_number, 9)) ? (int)substr($request->serial_number, 9) : '';
                if ($tipeJurnal == 'ANG') {

                    $jurnalableType = 'App\Models\Angsuran';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class], function ($query) use ($year, $month, $serialNumber, $startUntilPeriod, $endUntilPeriod) {

                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                    $jurnalableType = 'App\Models\AngsuranPartial';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->orwhereHasMorph('jurnalable', [AngsuranPartial::class], function ($query) use ($year, $month, $serialNumber, $startUntilPeriod, $endUntilPeriod) {

                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->orwhereHasMorph('jurnalable', [AngsuranPartial::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                } else if ($tipeJurnal == 'MTS') {
                    $jurnalableType = 'App\Models\JurnalTemp';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_posting', '=', $year)->whereMonth('tgl_posting', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                } else if ($tipeJurnal == 'TRU') {
                    $jurnalableType = 'App\Models\JurnalUmum';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {


                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                } else if ($tipeJurnal == 'TAR') {
                    $jurnalableType = 'App\Models\Penarikan';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                } else if ($tipeJurnal == 'PIJ') {
                    $jurnalableType = 'App\Models\Pinjaman';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number_kredit', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                } else if ($tipeJurnal == 'PCP') {
                    $jurnalableType = 'App\Models\Pinjaman';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_pelunasan', '=', $year)->whereMonth('tgl_pelunasan', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber,$startUntilPeriod, $endUntilPeriod) {
                            $query->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                        });
                    }
                } else if ($tipeJurnal == 'SIP') {
                    $jurnalableType = 'App\Models\Simpanan';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }
                }
            }
            if ($request->code) {
                $jurnal = $jurnal
                    ->where(function ($query) use ($request) {

                        $query->where('akun_debet', 'like', $request->code . '%')
                            ->orwhere('akun_kredit', 'like', $request->code . '%');
                    });
            }


            if ($request->keterangan) {
                $jurnal = $jurnal->where('keterangan', 'like', '%' . $request->keterangan . '%');
            }

            $jurnal = $jurnal->orderBy('tgl_transaksi', 'desc')->get();
            $data['jurnal'] = $jurnal;

            // share data to view
            view()->share('data', $data);
            $pdf = PDF::loadView('jurnal.excel', $data)->setPaper('a4', 'landscape');

            // download PDF file with download method
            $filename = 'export_jurnal_' . Carbon::now()->format('d M Y') . '.pdf';
            return $pdf->download($filename);
        } catch (\Throwable $e) {
            $message = class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }

    public function resumeData(Request $request){
        if (!$request->from) {
            $request->from = Carbon::today()->startOfMonth()->format('d-m-Y');
        }
        if (!$request->to) {
            $request->to = Carbon::today()->endOfMonth()->format('d-m-Y');
        }
        $month = strtotime($request->from);
        $end = strtotime($request->to);
        $rawdata=array();
        $i=0;
        while($month < $end)
        {
            $from = Carbon::createFromFormat('Y-m-d',date('Y-m-d',$month))->startOfMonth()->format('Y-m-d');
            $to = Carbon::createFromFormat('Y-m-d',date('Y-m-d',$month))->endOfMonth()->format('Y-m-d');
//            dd($from);
            $rawdata[$i]['bulan']=date('Y-m',$month) ;
            $dr= JurnalManager::jurnalTotalDr($from,$to);
            $cr= JurnalManager::jurnalTotalCr($from,$to);
            $rawdata[$i]['Dr']=$dr ;
            $rawdata[$i]['Cr']=$cr ;
            $rawdata[$i]['Selisih']=$dr-$cr ;
            $month = strtotime("+1 month", $month);
            $i++;
        }
//        dd($rawdata);
        $data['title'] = 'Resume Jurnal';
        $data['data'] = $rawdata;
        $data['request'] = $request;
        $data['tipeJurnal'] = TipeJurnal::get()->pluck('name', 'id');
        return view('jurnal.resume', $data);
    }

    public function destroy(Request $request,$id)
    { 
        if (isset($request->password)) {
            $user = Auth::user();
            $check = Hash::check($request->password, $user->password);
            if (!$check) {
                Log::error('Wrong Password');
                return response()->json(['message' => 'Wrong Password'], 412);
            }
        }
        $jurnal = Jurnal::findOrFail($id);
        $jurnalable = $jurnal->jurnalable;
        if ($jurnalable){
            $jurnalable->delete();
        }
        $jurnal->delete();
        // return redirect()->route('jurnal-list');
        return redirect()->back();
    }

    public function edit($id, Request $request)
    {
        $this->authorize('edit jurnal', Auth::user());
        // jika ada serial number, maka get jurnal by serial number
        if($request->serial_number)
        {
            // $startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->startOfDay()->format('Y-m-d');
            // $endUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->to)->endOfDay()->format('Y-m-d');
            // $jurnal = Jurnal::query();
            // $tipeJurnal = substr($request->serial_number, 0, 3);
            // $year = substr($request->serial_number, 3, 4);
            // $month = substr($request->serial_number, 7, 2);
            // $serialNumber = (substr($request->serial_number, 9)) ? (int)substr($request->serial_number, 9) : '';
            // if ($tipeJurnal == 'ANG') {

            //     $jurnalableType = 'App\Models\Angsuran';
            //     if ($year !== '' && $month !== '' && $serialNumber !== '') {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class], function ($query) use ($year, $month, $serialNumber, $startUntilPeriod, $endUntilPeriod) {

            //             $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);


            //         });

            //     } else {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
            //     }


            //     $jurnalableType = 'App\Models\AngsuranPartial';
            //     if ($year !== '' && $month !== '' && $serialNumber !== '') {
            //         $jurnal = $jurnal->orwhereHasMorph('jurnalable', [AngsuranPartial::class], function ($query) use ($year, $month, $serialNumber, $startUntilPeriod, $endUntilPeriod) {

            //             $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);

            //         });
            //     } else {
            //         $jurnal = $jurnal->orwhereHasMorph('jurnalable', [AngsuranPartial::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
            //     }


            // } else if ($tipeJurnal == 'MTS') {
            //     $jurnalableType = 'App\Models\JurnalTemp';
            //     if ($year !== '' && $month !== '' && $serialNumber !== '') {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class], function ($query) use ($year, $month, $serialNumber) {
            //             $query->whereYear('tgl_posting', '=', $year)->whereMonth('tgl_posting', '=', $month)->where('serial_number', $serialNumber);
            //         });
            //     } else {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
            //     }

            // } else if ($tipeJurnal == 'TRU') {
            //     $jurnalableType = 'App\Models\JurnalUmum';
            //     if ($year !== '' && $month !== '' && $serialNumber !== '') {


            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class], function ($query) use ($year, $month, $serialNumber) {
            //             $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
            //         });
            //     } else {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
            //     }


            // } else if ($tipeJurnal == 'TAR') {
            //     $jurnalableType = 'App\Models\Penarikan';
            //     if ($year !== '' && $month !== '' && $serialNumber !== '') {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class], function ($query) use ($year, $month, $serialNumber) {
            //             $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
            //         });
            //     } else {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
            //     }


            // } else if ($tipeJurnal == 'PIJ') {
            //     $jurnalableType = 'App\Models\Pinjaman';
            //     if ($year !== '' && $month !== '' && $serialNumber !== '') {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber) {
            //             $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number_kredit', $serialNumber);
            //         });
            //     } else {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
            //     }


            // } else if ($tipeJurnal == 'PCP') {
            //     $jurnalableType = 'App\Models\Pinjaman';
            //     if ($year !== '' && $month !== '' && $serialNumber !== '') {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber) {
            //             $query->whereYear('tgl_pelunasan', '=', $year)->whereMonth('tgl_pelunasan', '=', $month)->where('serial_number', $serialNumber);
            //         });
            //     } else {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber,$startUntilPeriod, $endUntilPeriod) {
            //             $query->whereBetween('tgl_pelunasan', [$startUntilPeriod, $endUntilPeriod]);
            //         });
            //     }

            // } else if ($tipeJurnal == 'SIP') {
            //     $jurnalableType = 'App\Models\Simpanan';
            //     if ($year !== '' && $month !== '' && $serialNumber !== '') {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class], function ($query) use ($year, $month, $serialNumber) {
            //             $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
            //         });
            //     } else {
            //         $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
            //     }
            // }
            $startUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->from)->startOfDay()->format('Y-m-d');
            $endUntilPeriod = Carbon::createFromFormat('d-m-Y', $request->to)->endOfDay()->format('Y-m-d');
            $jurnal = Jurnal::with('tipeJurnal', 'createdBy')->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
            if ($request->id_tipe_jurnal) {
                $jurnal = $jurnal->where('id_tipe_jurnal', $request->id_tipe_jurnal);
            }

            if ($request->serial_number) {
                $tipeJurnal = substr($request->serial_number, 0, 3);
                $year = substr($request->serial_number, 3, 4);
                $month = substr($request->serial_number, 7, 2);
                $serialNumber = (substr($request->serial_number, 9)) ? (int)substr($request->serial_number, 9) : '';
                if ($tipeJurnal == 'ANG') {

                    $jurnalableType = 'App\Models\Angsuran';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class], function ($query) use ($year, $month, $serialNumber, $startUntilPeriod, $endUntilPeriod) {

                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);


                        });

                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Angsuran::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                    $jurnalableType = 'App\Models\AngsuranPartial';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->orwhereHasMorph('jurnalable', [AngsuranPartial::class], function ($query) use ($year, $month, $serialNumber, $startUntilPeriod, $endUntilPeriod) {

                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);

                        });
                    } else {
                        $jurnal = $jurnal->orwhereHasMorph('jurnalable', [AngsuranPartial::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                } else if ($tipeJurnal == 'MTS') {
                    $jurnalableType = 'App\Models\JurnalTemp';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_posting', '=', $year)->whereMonth('tgl_posting', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalTemp::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }

                } else if ($tipeJurnal == 'TRU') {
                    $jurnalableType = 'App\Models\JurnalUmum';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {


                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [JurnalUmum::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                } else if ($tipeJurnal == 'TAR') {
                    $jurnalableType = 'App\Models\Penarikan';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Penarikan::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                } else if ($tipeJurnal == 'PIJ') {
                    $jurnalableType = 'App\Models\Pinjaman';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number_kredit', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                } else if ($tipeJurnal == 'PCP') {
                    $jurnalableType = 'App\Models\Pinjaman';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_pelunasan', '=', $year)->whereMonth('tgl_pelunasan', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Pinjaman::class], function ($query) use ($year, $month, $serialNumber,$startUntilPeriod, $endUntilPeriod) {
                            $query->whereBetween('tgl_pelunasan', [$startUntilPeriod, $endUntilPeriod]);
                        });
                    }

                } else if ($tipeJurnal == 'SIP') {
                    $jurnalableType = 'App\Models\Simpanan';
                    if ($year !== '' && $month !== '' && $serialNumber !== '') {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class], function ($query) use ($year, $month, $serialNumber) {
                            $query->whereYear('tgl_transaksi', '=', $year)->whereMonth('tgl_transaksi', '=', $month)->where('serial_number', $serialNumber);
                        });
                    } else {
                        $jurnal = $jurnal->whereHasMorph('jurnalable', [Simpanan::class])->whereBetween('tgl_transaksi', [$startUntilPeriod, $endUntilPeriod]);
                    }


                }
            }
            if ($request->keterangan) {
                $jurnal = $jurnal->where('keterangan', 'like', '%' . $request->keterangan . '%');
            }
            if ($request->code) {
                $jurnal = $jurnal
                    ->where(function ($query) use ($request) {

                        $query->where('akun_debet', 'like', $request->code . '%')
                            ->orwhere('akun_kredit', 'like', $request->code . '%');

                    });

            }

            $jumlahAnggota = $jurnal->get()->groupBy('anggota')->count();
            if ($jumlahAnggota > 1) {
                return redirect()->back()->withErrors('module edit jurnal beda anggota belum tersedia');
            }
            $jurnal = $jurnal->orderBy('tgl_transaksi', 'desc');
            $jurnal = $jurnal->get();
        }
        else
        {
            $jurnal = Jurnal::where('id', $id)->get();
        }
        // dd($jurnal);
        $coas = Cache::remember('coas', 120, function ()
        {
            return Code::select('CODE', 'NAMA_TRANSAKSI')->get();
        });
        $data['title'] = 'Edit Jurnal';
        $data['jurnals'] = $jurnal;
        $data['request'] = $request;
        $data['coas'] = $coas;
        // dd($coas);
        return view('jurnal.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $inputKredit = 0;
        $inputDebet = 0;
        foreach ($request->kredit as $key => $value) {
            $inputKredit = $inputKredit + $request->kredit[$key];
            $inputDebet = $inputDebet + $request->debet[$key];
        }

        if($inputKredit != $inputDebet)
        {
            return redirect()->back()->withErrors('jumlah debet tidak sesuai dengen jumlah kredit');
        }
        foreach ($request->kredit as $key => $value) 
        {
            $id = $key;
            $update = Jurnal::where('id',$id)->first();
            $tgl_transaksi = Carbon::createFromFormat('d-m-Y', $request->tgl_transaksi[$id]);
            // $update->jurnalable->besar_simpanan = $request->debet[$id];
            
            $jurnalable = $update->jurnalable;
            if ($update->jurnalable_type == 'App\Models\Simpanan') {
                $jurnalable->besar_simpanan = $request->kredit[$id];
                $jurnalable->tgl_transaksi = $tgl_transaksi;
                $jurnalable->kode_jenis_simpan = $request->akun_kredit[$id];
            } elseif ($update->jurnalable_type == 'App\Models\Penarikan') {
                $jurnalable->besar_ambil = $request->debet[$id];
                $jurnalable->tgl_transaksi = $tgl_transaksi;
                $jurnalable->code_trans = $request->akun_debet[$id];
            } elseif ($update->jurnalable_type == 'App\Models\Pinjaman') {
                $jurnalable->tgl_transaksi = $tgl_transaksi;
                $jurnalable->kode_jenis_pinjam = $request->akun_debet[$id];
                $jurnalable->besar_pinjam = $request->debet[$id];
                if ($request->akun_kredit[$id] == COA_JASA_PROVISI) {
                    $jurnalable->biaya_provisi = $request->kredit[$id];
                } elseif ($request->akun_kredit[$id] == COA_UTIP_ASURANSI) {
                    $jurnalable->biaya_asuransi = $request->kredit[$id];
                } elseif ($request->akun_kredit[$id] == COA_JASA_ADMINISTRASI) {
                    $jurnalable->biaya_administrasi = $request->kredit[$id];
                } elseif ($request->akun_kredit[$id] == COA_JASA_TOP_UP_PINJ_JANGKA_PANJANG || COA_JASA_TOP_UP_PINJ_JANGKA_PENDEK) {
                    $jurnalable->biaya_jasa_topup = $request->kredit[$id];
                } 
                // elseif ($request->akun_kredit[$id] == '701.02.001') {
                //     $jurnalable->service_fee = $request->kredit[$id];
                // }
            } elseif ($update->jurnalable_type == 'App\Models\Angsuran') {
                if ($request->akun_kredit[$id] == '106.02.020') {
                    $jurnalable->besar_angsuran = $request->kredit[$id];
                } elseif ($request->akun_kredit[$id] == '701.02.001') {
                    $jurnalable->jasa = $request->kredit[$id];
                }
            } 
            // elseif ($update->jurnalable_type == 'App\Models\AngsuranPartial') {
            //     if ($request->akun_kredit[$id] == '106.02.060') {
            //         $jurnalable->besar_angsuran = $request->kredit[$id];
            //     } elseif ($request->akun_kredit[$id] == '701.02.001') {
            //         $jurnalable->jasa = $request->kredit[$id];
            //     }
            //     $jurnalable->besar_pembayaran = $request->debet[$id];
            // }
            // elseif ($update->jurnalable_type == 'App\Models\JurnalUmum') {
            //     $jurnalable->tgl_transaksi = $tgl_transaksi;
            // } elseif ($update->jurnalable_type == 'App\Models\SaldoAwal') {
            //     $jurnalable->nominal = $request->kredit[$id];
            // } 
            $jurnalable->save();
    
            $update->tgl_transaksi = $tgl_transaksi;
            $update->akun_kredit = $request->akun_kredit[$id];
            $update->akun_debet = $request->akun_debet[$id];
            $update->debet = $request->debet[$id];
            $update->kredit = $request->kredit[$id];
            // $update->debet = $request->debet[$id];
            // $update->kredit = $request->kredit[$id];
            $update->save();
        }
        return redirect()->route('jurnal-list');
    }
}
