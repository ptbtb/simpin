<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Events\Pinjaman\PengajuanApproved;
use App\Exports\PinjamanExport;

use App\Managers\PinjamanManager;
use App\Models\Anggota;
use App\Models\AsuransiPinjaman;
use App\Models\Pengajuan;
use App\Models\Pinjaman;
use App\Models\JenisPinjaman;
use App\Models\Penghasilan;
use App\Models\View\ViewSaldo;
use Auth;
use Carbon\Carbon;
use DB;
use Excel;
use Illuminate\Support\Facades\Storage;
use PDF;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        // check role user
        if ($user->roles->first()->id == ROLE_ANGGOTA)
        {
            $anggota = $user->anggota;
            if (is_null($anggota))
            {
                return redirect()->back()->withError('Your account has no members');
            }
            
            $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                                    ->notPaid();
        }
        else
        {
            $listPinjaman = Pinjaman::notPaid();
        }

        if ($request->from)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','<=', $request->to);
        }
        $listPinjaman = $listPinjaman->get();
        $data['title'] = "List Pinjaman";
        $data['listPinjaman'] = $listPinjaman;
        $data['request'] = $request;
        return view('pinjaman.index',$data);
    }

    public function indexPengajuan(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pengajuan pinjaman', $user);

        if ($user->isAnggota())
        {
            $anggota = $user->anggota;
            if (is_null($anggota))
            {
                return redirect()->back()->withError('Your account has no members');
            }
            
            $listPengajuanPinjaman = Pengajuan::where('kode_anggota', $anggota->kode_anggota)
                                                ->get();
        }
        else
        {
            $listPengajuanPinjaman = Pengajuan::with('anggota')->get();
        }
        
        $data['title'] = "List Pengajuan Pinjaman";
        $data['listPengajuanPinjaman'] = $listPengajuanPinjaman;
        $data['request'] = $request;
        return view('pinjaman.indexPengajuan',$data);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view history pinjaman', $user);

        if ($user->roles->first()->id == ROLE_ANGGOTA)
        {
            $anggota = $user->anggota;
            if (is_null($anggota))
            {
                return redirect()->back()->withError('Your account has no members');
            }
            
            $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota)
                                    ->where('status', 'lunas');
        }
        else
        {
            $listPinjaman = Pinjaman::where('status', 'lunas');
        }

        if ($request->from)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','<=', $request->to);
        }
        $listPinjaman = $listPinjaman->get();
        $data['title'] = "History Pinjaman";
        $data['listPinjaman'] = $listPinjaman;
        $data['request'] = $request;
        return view('pinjaman.history',$data);
    }

    public function show($id)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        $pinjaman = Pinjaman::with('anggota','listAngsuran')
                            ->where('kode_pinjam', $id)
                            ->first();
        
        $data['pinjaman'] = $pinjaman;
        $data['jenisPinjaman'] = $pinjaman->jenisPinjaman;
        return view('pinjaman.detail', $data);
    }

    public function downloadFormPinjaman(Request $request){
        $user = Auth::user();
        $this->authorize('download form pinjaman', $user);
        $data['title'] = 'Download Form Pinjaman';
        $data['listJenisPinjaman'] = JenisPinjaman::all();
        return view('pinjaman.downloadFormPinjaman', $data);
    }

    public function createExcel(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);
        $anggota = $user->anggota;
        $request->anggota = $anggota;
        $filename = 'export_pinjaman_excel_'.Carbon::now()->format('d M Y').'.xlsx';
        return Excel::download(new PinjamanExport($request), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function createPDF(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);

        if ($user->roles->first()->id == ROLE_ANGGOTA)
        {
            $anggota = $user->anggota;
            if (is_null($anggota))
            {
                return redirect()->back()->withError('Your account has no members');
            }
            
            $listPinjaman = Pinjaman::where('kode_anggota', $anggota->kode_anggota);
        }
        else
        {
            $listPinjaman = Pinjaman::with('anggota');
        }

        if ($request->from)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','>=', $request->from);
        }
        if ($request->to)
        {
            $listPinjaman = $listPinjaman->where('tgl_entri','<=', $request->to);
        }
        if ($request->status)
        {
            $listPinjaman = $listPinjaman->where('status', $request->status);
        }

        $listPinjaman = $listPinjaman->get();

        // share data to view
        view()->share('listPinjaman',$listPinjaman);
        $pdf = PDF::loadView('pinjaman.excel', $listPinjaman)->setPaper('a4', 'landscape');
  
        // download PDF file with download method
        $filename = 'export_pinjaman_'.Carbon::now()->format('d M Y').'.pdf';
        return $pdf->download($filename);
    }

    public function createPDF1(Request $request)
    {
        $user = Auth::user();
        $this->authorize('view pinjaman', $user);
        $anggota = $user->anggota;
        $request->anggota = $anggota;
        $filename = 'export_pinjaman_excel_'.Carbon::now()->format('d M Y').'.pdf';
        return Excel::download(new PinjamanExport($request), $filename, \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function createPengajuanPinjaman()
    {
        $user = Auth::user();
        $this->authorize('add pengajuan pinjaman', $user);
        $data['title'] = 'Buat Pengajuan Pinjaman';
        $data['listJenisPinjaman'] = JenisPinjaman::all();
        return view('pinjaman.createPengajuanPinjaman', $data);
    }

    public function storePengajuanPinjaman(Request $request)
    {
        $user = Auth::user();
        $this->authorize('add pengajuan pinjaman', $user);

        //  chek pengajuan yang belum accepted
        $jenisPinjaman = JenisPinjaman::find($request->jenis_pinjaman);
        $checkPengajuan = Pengajuan::where('kode_jenis_pinjam', $jenisPinjaman->kode_jenis_pinjam)
                                    ->notApproved()
                                    ->where('kode_anggota', $request->kode_anggota)
                                    ->get();
        
        if ($checkPengajuan->count())
        {
            return redirect()->back()->withError('Pengajuan pinjaman gagal. Anda sudah pernah mengajukan pinjaman untuk jenis pinjaman '. $jenisPinjaman->nama_pinjaman);
        }

        // check pinjaman yang belum lunas
        $checkPinjaman = Pinjaman::where('kode_jenis_pinjam', $jenisPinjaman->kode_jenis_pinjam)
                                ->notPaid()
                                ->where('kode_anggota', $request->kode_anggota)
                                ->get();

        if ($checkPinjaman->count())
        {
            return redirect()->back()->withError('Pengajuan pinjaman gagal. Anda masih memiliki pinjaman dengan jenis pinjaman '. $jenisPinjaman->nama_pinjaman.' yang belum lunas');
        }
        
        $besarPinjaman = filter_var($request->besar_pinjaman, FILTER_SANITIZE_NUMBER_INT);
        $maksimalPinjaman = filter_var($request->maksimal_besar_pinjaman, FILTER_SANITIZE_NUMBER_INT);

        if ($maksimalPinjaman < $besarPinjaman)
        {
            return redirect()->back()->withError('Pengajuan pinjaman gagal. Jumlah pinjaman yang anda ajukan melebihi batas maksimal peminjaman.');
        }

        // check gaji
        // $gaji = Penghasilan::where('kode_anggota', $request->kode_anggota)->first()->gaji_bulanan;
        // $potonganGaji = 0.65*$gaji;
        // $angsuranPerbulan = $request->besar_pinjam/$request->lama_angsuran;
        // if ($potonganGaji > $angsuranPerbulan)
        // {
        //     # code...
        // }

        DB::transaction(function () use ($request, $besarPinjaman, $user)
        {
            $kodeAnggota = $request->kode_anggota;
            $kodePengajuan = str_replace('.','',$request->jenis_pinjaman).'-'.$kodeAnggota.'-'.Carbon::now()->format('dmYHis');
            $pengajuan = new Pengajuan();
            $pengajuan->kode_pengajuan = $kodePengajuan;
            $pengajuan->tgl_pengajuan = Carbon::now();
            $pengajuan->kode_anggota = $request->kode_anggota;
            $pengajuan->kode_jenis_pinjam = $request->jenis_pinjaman;
            $pengajuan->besar_pinjam = $besarPinjaman;
            $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_KONFIRMASI;
            $pengajuan->created_by = $user->id;

            $file = $request->form_persetujuan;
			if ($file)
			{
				$config['disk'] = 'upload';
				$config['upload_path'] = '/pengajuanpinjaman/'.$user->id.'/form'; 
				$config['public_path'] = env('APP_URL') . '/upload/pengajuanpinjaman/'.$user->id.'/form';

				// create directory if doesn't exist
				if (!Storage::disk($config['disk'])->has($config['upload_path']))
				{
					Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
				}

				// upload file if valid
				if ($file->isValid())
				{
					$filename = uniqid() .'.'. $file->getClientOriginalExtension();

					Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file, $filename);
					$pengajuan->form_persetujuan = $config['disk'].$config['upload_path'].'/'.$filename;
				}
            }
            
            $pengajuan->save(); 
        });
        
        return redirect()->route('pengajuan-pinjaman-add')->withSuccess('Pengajuan pinjaman telah dibuat dan menunggu persetujuan.');
    }

    public function updateStatusPengajuanPinjaman(Request $request)
    {
        try
        {
            $user = Auth::user();
            $pengajuan = Pengajuan::find($request->id);
            if($request->action == CANCEL_PENGAJUAN_PINJAMAN)
            {
                $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_DIBATALKAN;
                $pengajuan->save();
                return response()->json(['message' => 'success'], 200);
            }

            $this->authorize('approve pengajuan pinjaman', $user);
            if (is_null($pengajuan))
            {
                return response()->json(['message' => 'not found'], 404);
            }
            if ($request->action == APPROVE_PENGAJUAN_PINJAMAN)
            {
                $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN;
                $pengajuan->tgl_acc = Carbon::now();
                $pengajuan->approved_by = $user->id;
                $pengajuan->save();
                event(new PengajuanApproved($pengajuan));
            }
            else
            {
                $pengajuan->id_status_pengajuan = STATUS_PENGAJUAN_PINJAMAN_DITOLAK;
                $pengajuan->save();
            }
            return response()->json(['message' => 'success'], 200);
        }
        catch (\Exception $e)
        {
            \Log::error($e);
            $message = $e->getMessage();
            return response()->json(['message' => $message], 500);
        }
    }

    public function calculateMaxPinjaman(Request $request)
    {
        $jenisPinjaman = JenisPinjaman::find($request->id_jenis_pinjaman);
        if (is_null($jenisPinjaman))
        {
            return 0;
        }
        $anggota = Anggota::find($request->kode_anggota);
        if (is_null($anggota))
        {
            return 0;
        }
        
        if ($jenisPinjaman->isJangkaPanjang())
        {
            if ($anggota->isPensiunan())
            {
                $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                return $saldo->jumlah * 0.75;
            }
            elseif ($anggota->isAnggotaBiasa())
            {
                if ($jenisPinjaman->isDanaKopegmar())
                {
                    $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                    return $saldo->jumlah * 5;
                }
                elseif($jenisPinjaman->isDanaLain())
                {
                    $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                    return $saldo->jumlah * 8;
                }
            }
            elseif($anggota->isAnggotaLuarBiasa())
            {
                $company = $anggota->company;
                if ($company->isKopegmarGroup())
                {
                    return 30000000;
                }
                if ($company->isKojaGroup())
                {
                    $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                    return $saldo->jumlah * 5;
                }
            }
        }
        elseif($jenisPinjaman->isJangkaPendek())
        {
            $penghasilanTertentu = $anggota->listPenghasilanTertentu;
            if (!$penghasilanTertentu->count())
            {
                return response()->json(['message' => 'Tidak memiliki penghasilan tertentu'], 412);
            }

            $jumlahPenghasilanTertentu = $penghasilanTertentu->sum('value');
            if ($anggota->isAnggotaBiasa())
            {
                return 100000000;
            }
            elseif($anggota->isAnggotaLuarBiasa())
            {
                return 100000000;
            }
            elseif($anggota->isPensiunan())
            {
                $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                return $saldo->jumlah * 0.75;
            }
        }
        return 0;
    }

    public function simulasiPinjaman(Request $request)
    {
        $anggota = Anggota::find($request->kode_anggota);
        $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
        $jenisPinjaman = JenisPinjaman::find($request->jenis_pinjaman);
        $besarPinjaman = filter_var($request->besar_pinjaman, FILTER_SANITIZE_NUMBER_INT);
        $maksimalBesarPinjaman = filter_var($request->maksimal_besar_pinjaman, FILTER_SANITIZE_NUMBER_INT);
        $lamaAngsuran = $request->lama_angsuran;
        $biayaAdministrasi = $jenisPinjaman->kategoriJenisPinjaman->biaya_admin;
        $provisi = 0;
        if ($jenisPinjaman->isDanaLain())
        {
            $provisi = 0.01;
        }
        $asuransiPinjaman = AsuransiPinjaman::where('lama_pinjaman', $jenisPinjaman->lama_angsuran)
                                                ->where('kategori_jenis_pinjaman_id', $jenisPinjaman->kategori_jenis_pinjaman_id)
                                                ->first();

        $angsuranPokok = $besarPinjaman/$lamaAngsuran;
        $asuransi = 0;
        if ($asuransiPinjaman)
        {
            $asuransi = $asuransiPinjaman->besar_asuransi/100;
        }
        $asuransiPerbulan = $angsuranPokok*$asuransi;

        $jasaPerbulan = $angsuranPokok*$jenisPinjaman->kategoriJenisPinjaman->jasa/100;
        if ($besarPinjaman > 100000000 && $jenisPinjaman->lama_angsuran > 3)
        {
            $jasaPerbulan = $angsuranPokok*3/100;
        }
        $angsuranPerbulan = $angsuranPokok + $asuransiPerbulan + $jasaPerbulan;
        $collection = [
            'anggota' => $anggota,
            'saldo'=> $saldo,
            'jenisPinjaman'=> $jenisPinjaman,
            'besarPinjaman'=> $besarPinjaman,
            'maksimalBesarPinjaman'=> $maksimalBesarPinjaman,
            'lamaAngsuran'=> $lamaAngsuran,
            'biayaAdministrasi'=> $biayaAdministrasi,
            'provisi'=> $provisi,
            'asuransiPerbulan'=> $asuransiPerbulan,
            'jasaPerbulan'=> $jasaPerbulan,
            'angsuranPerbulan'=> $angsuranPerbulan,
            'angsuranPokok'=> $angsuranPokok,
        ];

        $data = $collection;
        $data['collection'] = $collection;
        $data['title'] = 'Download Form Pinjaman';;
        return view('pinjaman.hasilSimulasi', $data);
    }

    public function generateFormPinjaman (Request $request)
    {
        $anggota = Anggota::find($request->anggota);
        $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
        $jenisPinjaman = JenisPinjaman::find($request->jenisPinjaman);
        $besarPinjaman = filter_var($request->besarPinjaman, FILTER_SANITIZE_NUMBER_INT);
        $maksimalBesarPinjaman = filter_var($request->maksimalBesarPinjaman, FILTER_SANITIZE_NUMBER_INT);
        $lamaAngsuran = $request->lamaAngsuran;
        $biayaAdministrasi = $jenisPinjaman->kategoriJenisPinjaman->biaya_admin;
        $provisi = 0;
        if ($jenisPinjaman->isDanaLain())
        {
            $provisi = 0.01;
        }
        $asuransiPinjaman = AsuransiPinjaman::where('lama_pinjaman', $jenisPinjaman->lama_angsuran)
                                                ->where('kategori_jenis_pinjaman_id', $jenisPinjaman->kategori_jenis_pinjaman_id)
                                                ->first();

        $angsuranPokok = $besarPinjaman/$lamaAngsuran;
        $asuransi = 0;
        if ($asuransiPinjaman)
        {
            $asuransi = $asuransiPinjaman->besar_asuransi/100;
        }
        $asuransiPerbulan = $angsuranPokok*$asuransi;

        $jasaPerbulan = $angsuranPokok*$jenisPinjaman->kategoriJenisPinjaman->jasa/100;
        if ($besarPinjaman > 100000000 && $jenisPinjaman->lama_angsuran > 3)
        {
            $jasaPerbulan = $angsuranPokok*3/100;
        }
        $angsuranPerbulan = $angsuranPokok + $asuransiPerbulan + $jasaPerbulan;
        $terbilang = self::terbilang($besarPinjaman).' rupiah';
        $data = [
            'anggota' => $anggota,
            'saldo'=> $saldo,
            'jenisPinjaman'=> $jenisPinjaman,
            'besarPinjaman'=> $besarPinjaman,
            'besarPinjamanTerbilang' => $terbilang,
            'maksimalBesarPinjaman'=> $maksimalBesarPinjaman,
            'lamaAngsuran'=> $lamaAngsuran,
            'lamaAngsuranTerbilang' => self::terbilang($lamaAngsuran),
            'biayaAdministrasi'=> $biayaAdministrasi,
            'provisi'=> $provisi,
            'asuransiPerbulan'=> $asuransiPerbulan,
            'jasaPerbulan'=> $jasaPerbulan,
            'angsuranPerbulan'=> $angsuranPerbulan,
            'angsuranPokok'=> $angsuranPokok,
        ];

        view()->share('data',$data);
        PDF::setOptions(['margin-left' => 0,'margin-right' => 0]);
        $pdf = PDF::loadView('pinjaman.formPersetujuan', $data)->setPaper('a4', 'portrait');

        // download PDF file with download method
        $filename = 'form_persetujuan_atasan'.Carbon::now()->format('d M Y').'.pdf';
        return $pdf->download($filename);
        
        return view('pinjaman.formPersetujuan', $data);
    }

    static function penyebut($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = self::penyebut($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = self::penyebut($nilai/10)." puluh". self::penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . self::penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = self::penyebut($nilai/100) . " ratus" . self::penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . self::penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = self::penyebut($nilai/1000) . " ribu" . self::penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = self::penyebut($nilai/1000000) . " juta" . self::penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = self::penyebut($nilai/1000000000) . " milyar" . self::penyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = self::penyebut($nilai/1000000000000) . " trilyun" . self::penyebut(fmod($nilai,1000000000000));
		}     
		return $temp;
	}

	static function terbilang($nilai) {
		if($nilai<0) {
			$hasil = "minus ". trim(self::penyebut($nilai));
		} else {
			$hasil = trim(self::penyebut($nilai));
		}     		
		return $hasil;
	}
}