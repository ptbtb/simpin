<?php

namespace App\Http\Controllers\Api;

use App\Events\Pinjaman\PengajuanCreated;
use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\JenisPinjaman;
use App\Models\Pengajuan;
use App\Models\Penghasilan;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PengajuanPinjamanController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $user = $request->user('api');
            if (is_null($user))
            {
                $response = [
                    'message' => 'User tidak ditemukan'
                ];

                return response()->json($response, 404);
            }

            $anggota = $user->anggota;
            if (is_null($anggota))
            {
                $response = [
                    'message' => 'Hanya anggota yang dapat mengakses menu ini'
                ];

                return response()->json($response, 403);
            }

            $listPengajuan = Pengajuan::with('anggota','jenisPinjaman','statusPengajuan','jenisPenghasilan', 'approvedBy')
                                        ->where('kode_anggota', $anggota->kode_anggota)
                                        ->get();

            $message = null;
            if ($listPengajuan->count() == 0)
            {
                $message = 'List pengajuan pinjaman kososng';
            }

            $data = $listPengajuan->map(function ($pengajuan)
            {
                return [
                    'kode_pengajuan' => $pengajuan->kode_pengajuan,
                    'tgl_pengajuan' => $pengajuan->tgl_pengajuan->toDateString(),
                    'jenis_pinjaman' => [
                                            'kode' => $pengajuan->jenisPinjaman->kode_jenis_pinjam,
                                            'nama' => $pengajuan->jenisPinjaman->nama_pinjaman
                                        ],
                    'besar_pinjaman' => $pengajuan->besar_pinjam,
                    'status_pengajuan' => $pengajuan->statusPengajuan->only('id','name')
                ];
            });

            $response = [
                'message' => $message,
                'data' => $data
            ];

            return response()->json($response, 200);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            $response['message'] = API_DEFAULT_ERROR_MESSAGE;
            return response()->json($response, 500);
        }
    }

    public function show(Request $request, $kode_pengajuan)
    {
        try
        {
            $user = $request->user('api');
            if (is_null($user))
            {
                $response = [
                    'message' => 'User tidak ditemukan'
                ];

                return response()->json($response, 404);
            }

            $anggota = $user->anggota;
            if (is_null($anggota))
            {
                $response = [
                    'message' => 'Hanya anggota yang dapat mengakses menu ini'
                ];

                return response()->json($response, 403);
            }

            $pengajuan = Pengajuan::with('anggota','jenisPinjaman','statusPengajuan','jenisPenghasilan', 'approvedBy')
                                ->where('kode_anggota', $anggota->kode_anggota)
                                ->where('kode_pengajuan', $kode_pengajuan)
                                ->first();
            
            if(is_null($pengajuan))
            {
                $response = [
                    'message' => 'Pengajuan pinjaman tidak ditemukan'
                ];

                return response()->json($response, 404);
            }

            $data = [
                'kode_pengajuan' => $pengajuan->kode_pengajuan,
                'tgl_pengajuan' => $pengajuan->tgl_pengajuan->toDateString(),
                'anggota' => $pengajuan->anggota->only('kode_anggota','nama_anggota'),
                'jenis_pinjaman' => [
                                        'kode' => $pengajuan->jenisPinjaman->kode_jenis_pinjam,
                                        'nama' => $pengajuan->jenisPinjaman->nama_pinjaman
                                    ],
                'besar_pinjaman' => $pengajuan->besar_pinjam,
                'form_persetujuan' => asset($pengajuan->form_persetujuan),
                'status_pengajuan' => $pengajuan->statusPengajuan->only('id','name'),
                'jenis_penghasilan' => $pengajuan->jenisPenghasilan->only('id','name')
            ];

            $response = [
                'message' => null,
                'data' => $data
            ];

            return response()->json($response, 200);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            $response['message'] = API_DEFAULT_ERROR_MESSAGE;
            return response()->json($response, 500);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $response = [];
            $rules = [
                'kode_anggota' => 'required',
                'id_jenis_pinjaman' => 'required',
                'id_jenis_penghasilan' => 'required',
                'besar_pinjaman' => 'required',
                'form_persetujuan_atasan' => 'required',
                'max_pinjaman' => 'required',
                'lama_angsuran' => 'required',
                'keperluan' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
            {
                $fields = array_keys($validator->errors()->toArray());
                $response = [
                    'message' => implode(', ', $fields). ' field are required'
                ];

                return response()->json($response, 404);
            }

            $user = $request->user('api');
            $anggota = Anggota::where('kode_anggota', $request->kode_anggota)
                                ->whereHas('user', function ($query) use ($user)
                                {
                                    return $query->where('id', $user->id);
                                })
                                ->first();

            if(is_null($anggota))
            {
                $response = [
                    'message' => 'Anggota not found'
                ];

                return response()->json($response, 404);
            }

            $besarPinjaman = filter_var($request->besar_pinjaman, FILTER_SANITIZE_NUMBER_INT);
            $maksimalPinjaman = filter_var($request->max_pinjaman, FILTER_SANITIZE_NUMBER_INT);

            //  chek pengajuan yang belum accepted
            $jenisPinjaman = JenisPinjaman::find($request->id_jenis_pinjaman);
            $checkPengajuan = Pengajuan::whereraw("SUBSTRING(kode_jenis_pinjam,1,6)=" . substr($jenisPinjaman->kode_jenis_pinjam, 0, 6) . " ")
                                        ->notApproved()
                                        ->where('kode_anggota', $anggota->kode_anggota)
                                        ->get();

            if ($checkPengajuan->count())
            {
                $response = [
                    'message' => 'Pengajuan pinjaman gagal. Anda sudah pernah mengajukan pinjaman untuk jenis pinjaman ' . $jenisPinjaman->nama_pinjaman
                ];

                return response()->json($response, 412);
            }

            
            // check pinjaman yang belum lunas
            $checkPinjaman = Pinjaman::whereraw("SUBSTRING(kode_jenis_pinjam,1,6)=" . substr($jenisPinjaman->kode_jenis_pinjam, 0, 6) . " ")
                                    ->notPaid()
                                    ->where('kode_anggota', $anggota->kode_anggota)
                                    ->get();

            if ($checkPinjaman->count())
            {
                $response = [
                    'message' => 'Pengajuan pinjaman gagal. Anda masih memiliki pinjaman dengan jenis pinjaman ' . $jenisPinjaman->nama_pinjaman . ' yang belum lunas'
                ];

                return response()->json($response, 412);
            }

            if ($maksimalPinjaman < $besarPinjaman)
            {
                $response = [
                    'message' => 'Pengajuan pinjaman gagal. Jumlah pinjaman yang anda ajukan melebihi batas maksimal peminjaman.'
                ];

                return response()->json($response, 412);
            }

            //check gaji
            $gaji = Penghasilan::where('kode_anggota', $anggota->kode_anggota)
                            ->where('id_jenis_penghasilan', JENIS_PENGHASILAN_GAJI_BULANAN)
                            ->first();

            if (is_null($gaji))
            {
                $response = [
                    'message' => 'Belum memilik penghasilan.'
                ];

                return response()->json($response, 412);
            }
            $gaji = $gaji->value;
            $potonganGaji = 0.65 * $gaji;
            $angsuranPerbulan = $besarPinjaman / $request->lama_angsuran;
            
            if ($angsuranPerbulan > $potonganGaji)
            {
                $response = [
                    'message' =>'Pengajuan pinjaman gagal. Jumlah pinjaman yang anda ajukan melebihi batas 65 % gaji Anda.'
                ];

                return response()->json($response, 412);
            }


            $pengajuan = null;
            DB::transaction(function () use ($request, $besarPinjaman, $user, &$pengajuan, $jenisPinjaman, $anggota) {
                $kodeAnggota = $anggota->kode_anggota;
                $kodePengajuan = str_replace('.', '', $jenisPinjaman->kode_jenis_pinjam) . '-' . $kodeAnggota . '-' . Carbon::now()->format('dmYHis');

                $pengajuan = new Pengajuan();
                $pengajuan->kode_pengajuan = $kodePengajuan;
                $pengajuan->tgl_pengajuan = Carbon::now();
                $pengajuan->kode_anggota = $kodeAnggota;
                $pengajuan->kode_jenis_pinjam = $jenisPinjaman->kode_jenis_pinjam;
                $pengajuan->besar_pinjam = $besarPinjaman;
                $pengajuan->keperluan = $request->keperluan;
                $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_KONFIRMASI;
                $pengajuan->sumber_dana = $request->id_jenis_penghasilan;
                $pengajuan->created_by = $user->id;

                $file = $request->form_persetujuan;
                if ($file) {
                    $config['disk'] = 'upload';
                    $config['upload_path'] = '/pengajuanpinjaman/' . $user->id . '/form';
                    $config['public_path'] = env('APP_URL') . '/upload/pengajuanpinjaman/' . $user->id . '/form';

                    // create directory if doesn't exist
                    if (!Storage::disk($config['disk'])->has($config['upload_path'])) {
                        Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
                    }

                    // upload file if valid
                    if ($file->isValid()) {
                        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                        Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file, $filename);
                        $pengajuan->form_persetujuan = $config['disk'] . $config['upload_path'] . '/' . $filename;
                    }
                }

                $pengajuan->save();
            });

            $message = null;
            if ($pengajuan)
            {
                event(new PengajuanCreated($pengajuan));

                $message = 'Berhasil mengajukan pinjaman';
                $data = [
                    'kode_pengajuan' => $pengajuan->kode_pengajuan,
                    'tgl_pengajuan' => $pengajuan->tgl_pengajuan->toDateString(),
                    'anggota' => $pengajuan->anggota->only('kode_anggota','nama_anggota'),
                    'jenis_pinjaman' => [
                                            'kode' => $pengajuan->jenisPinjaman->kode_jenis_pinjam,
                                            'nama' => $pengajuan->jenisPinjaman->nama_pinjaman
                                        ],
                    'besar_pinjaman' => $pengajuan->besar_pinjam,
                    'form_persetujuan' => asset($pengajuan->form_persetujuan),
                    'status_pengajuan' => $pengajuan->statusPengajuan->only('id','name'),
                    'jenis_penghasilan' => $pengajuan->jenisPenghasilan->only('id','name')
                ];
            }

            $response = [
                'message' => $message,
                'data' => $data
            ];
            return response()->json($response, 200);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            $response['message'] = API_DEFAULT_ERROR_MESSAGE;
            return response()->json($response, 500);
        }
    }
}
