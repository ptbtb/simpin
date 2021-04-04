<?php

namespace App\Http\Controllers;

use App\Events\Anggota\AnggotaCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Exports\AnggotaExport;
use App\Imports\AnggotaImport;
use App\Models\Anggota;
use App\Models\Company;
use App\Models\JenisAnggota;
use App\Models\KelasCompany;
use App\Models\JenisPenghasilan;
use App\Models\Penghasilan;

use Auth;
use Excel;
use PDF;
use Carbon\Carbon;
use Storage;

class AnggotaController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('view anggota', Auth::user());
        $data['companies'] = Company::all();
        $data['jenisAnggotas'] = JenisAnggota::all();
        $data['request'] = $request;
        $data['title'] = 'List Anggota';
        return view('anggota.index', $data);
    }

    public function indexAJax(Request $request)
    {
        $anggotas = Anggota::with('jenisAnggota');
        if ($request->status)
        {
            $anggotas = $anggotas->where('status', $request->status);
        }
        if ($request->id_jenis_anggota)
        {
            $anggotas = $anggotas->where('id_jenis_anggota', $request->id_jenis_anggota);
        }

        $anggotas = $anggotas->get();

        return $anggotas;
    }

    public function create()
    {
        $this->authorize('add anggota', Auth::user());
        $nomer = Anggota::max('kode_anggota');
        $data['title'] = 'Tambah Anggota';
        $data['nomer'] = $nomer + 1;
        $data['companies'] = Company::all();
        $data['jenisAnggotas'] = JenisAnggota::all();
        $data['kelasCompany'] = "";
        return view('/anggota/create', $data);
    }

    public function edit($id) {
        $this->authorize('edit anggota', Auth::user());
        $anggota = Anggota::find($id);
        $listJenisPenghasilan = JenisPenghasilan::show()
												->orderBy('sequence','asc')
                                                ->get();

        $listPenghasilan = $anggota->listPenghasilan;
        $data['listPenghasilan'] = null;
        if ($listPenghasilan->count())
        {
            $data['listPenghasilan'] = $listPenghasilan;
        }
        $data['listJenisPenghasilan'] = $listJenisPenghasilan;
        $data['title'] = 'Tambah Anggota';
        $data['companies'] = Company::all();
        $data['jenisAnggotas'] = JenisAnggota::all();
        // $data['kelasCompany'] = '';
        $data['kelasCompany'] = KelasCompany::where('company_id', $anggota->company_id)
                                            ->where('id_jenis_anggota', $anggota->id_jenis_anggota)
                                            ->get();
        $data['anggota'] = $anggota;
        return view('/anggota/edit', $data);
    }

    public function store(Request $request) {
        $this->authorize('add anggota', Auth::user());
        try
        {
            $anggota = Anggota::where('email',$request->email)
                            ->orWhere('kode_anggota', $request->kode_anggota)
                            ->first();

            if ($anggota)
            {
                $message = "Gagal menambahkan anggota. Email atau kode anggota sudah pernah terdaftar dalam sistem";
                return redirect()->back()->withError($message);
            }


            DB::transaction(function () use ($request)
            {
                $companyId = Company::find($request->company);
                $jenisAnggotaId = JenisAnggota::find($request->jenis_anggota);
                $kelasCompanyId = KelasCompany::find($request->kelas_company);
                $anggota = Anggota::create([
                    'kode_anggota' => $request->kode_anggota,
                    // 'kode_tabungan' =>  $request->kode_anggota,
                    'company_id' => $companyId->id,
                    'id_jenis_anggota' => $jenisAnggotaId->id_jenis_anggota,
                    'kelas_company_id' => $kelasCompanyId->id,
                    'tgl_masuk' => $request->tgl_masuk,
                    'nama_anggota' => $request->nama_anggota,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tempat_lahir' => $request->tmp_lahir,
                    'tgl_lahir' => $request->tgl_lahir,
                    'alamat_anggota' => $request->alamat_anggota,
                    'telp' => $request->telp,
                    'lokasi_kerja' => $request->lokasi_kerja,
                    'u_entry' => $request->u_entry,
                    'ktp' => $request->ktp,
                    'nipp' => $request->nipp,
                    'no_rek' => $request->no_rek,
                    'email' => $request->email,
                    'emergency_kontak' => $request->emergency_kontak,
                    'status' => 'aktif'
                ]);
                $password = $request->password;
                event(new AnggotaCreated($anggota, $password));
            });
            // alihkan halaman tambah buku ke halaman books

            return redirect()->route('anggota-list')->withSuccess('Data anggota Berhasil Ditambahkan');
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
				$message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
        }
    }

    public function update(Request $request, $id) {
        $this->authorize('edit anggota', Auth::user());
        try
        {
            $Anggota = Anggota::find($id);
            $Anggota->company_id= $request->company;
            $Anggota->id_jenis_anggota = $request->jenis_anggota;
            $Anggota->kelas_company_id = $request->kelas_company;
            $Anggota->tgl_masuk = $request->tgl_masuk;
            $Anggota->nama_anggota = $request->nama_anggota;
            $Anggota->jenis_kelamin = $request->jenis_kelamin;
            $Anggota->tempat_lahir = $request->tmp_lahir;
            $Anggota->tgl_lahir = $request->tgl_lahir;
            $Anggota->alamat_anggota = $request->alamat_anggota;
            $Anggota->telp = $request->telp;
            $Anggota->lokasi_kerja = $request->lokasi_kerja;
            $Anggota->u_entry = $request->u_entry;
            $Anggota->ktp = $request->ktp;
            $Anggota->nipp = $request->nipp;
            $Anggota->no_rek = $request->no_rek;
            $Anggota->email = $request->email;
            $Anggota->emergency_kontak = $request->emergency_kontak;
            $Anggota->status = 'aktif';

            // save file KTP
            $file_ktp = $request->ktp_photo;
            $user = $Anggota->user;
			if ($file_ktp)
			{
				$config['disk'] = 'upload';
				$config['upload_path'] = '/user/'.$Anggota->kode_anggota.'/ktp';
				$config['public_path'] = env('APP_URL') . '/upload/user/'.$Anggota->kode_anggota.'/ktp';
				if (!Storage::disk($config['disk'])->has($config['upload_path']))
				{
					Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
				}
				if ($file_ktp->isValid())
				{
					$filename = uniqid() .'.'. $file_ktp->getClientOriginalExtension();

					Storage::disk($config['disk'])->putFileAs($config['upload_path'], $file_ktp, $filename);
					$Anggota->foto_ktp = $config['disk'].$config['upload_path'].'/'.$filename;
				}
            }

            $Anggota->save();

            // save penghasilan
            $requestPenghasilan = $request->penghasilan;
			foreach ($requestPenghasilan as $key => $value)
			{
				$penghasilan = Penghasilan::where('id_jenis_penghasilan', $key)
											->where('kode_anggota', $Anggota->kode_anggota)
                                            ->first();
				if (is_null($penghasilan))
				{
					$penghasilan = new Penghasilan();
					$penghasilan->id_jenis_penghasilan = $key;
					$penghasilan->kode_anggota = $Anggota->kode_anggota;
                }
				$val = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
				if ($val)
				{
					$penghasilan->value = $val;
				}
				$penghasilan->save();
			}


			// for file upload
			$fileRequestPenghasilan = $request->file_penghasilan;
			if(!is_null($fileRequestPenghasilan))
			{
				foreach ($fileRequestPenghasilan as $key => $value)
				{
					$penghasilan = Penghasilan::where('id_jenis_penghasilan', $key)
												->where('kode_anggota', $Anggota->kode_anggota)
												->first();

					if (is_null($penghasilan))
					{
						$penghasilan = new Penghasilan();
						$penghasilan->id_jenis_penghasilan = $key;
						$penghasilan->kode_anggota = $Anggota->kode_anggota;
					}

					$config['disk'] = 'upload';
					$config['upload_path'] = '/user/'.$Anggota->kode_anggota.'/penghasilan';
					$config['public_path'] = env('APP_URL') . '/upload/user/'.$Anggota->kode_anggota.'/penghasilan';

					// create directory if doesn't exist
					if (!Storage::disk($config['disk'])->has($config['upload_path']))
					{
						Storage::disk($config['disk'])->makeDirectory($config['upload_path']);
					}

					// upload file if valid
					if ($value->isValid())
					{
						$filename = uniqid() .'.'. $value->getClientOriginalExtension();

						Storage::disk($config['disk'])->putFileAs($config['upload_path'], $value, $filename);
						$penghasilan->file_path = $config['disk'].$config['upload_path'].'/'.$filename;
					}
					$penghasilan->save();
				}
            }

            // alihkan halaman tambah buku ke halaman books
            return redirect()->route('anggota-list')->withSuccess('Data anggota Berhasil Dirubah');
        }
        catch (\Exception $e)
        {

            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
				$message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
        }
    }

    public function delete($ids) {
        $this->authorize('delete anggota', Auth::user());
        $Anggota = Anggota::destroy($ids);

        return redirect()->route('anggota-list')->withSuccess('Data anggota Berhasil Dihapus');
    }

    public function ajaxDetail($id)
    {
        $anggota = Anggota::find($id);
        $data['anggota'] = $anggota;
        return view('anggota.ajaxDetail', $data);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        if($search == ''){
            $anggotas = Anggota::orderby('nama_anggota','asc')->select('kode_anggota','nama_anggota')->limit(5)->where('status','aktif')->get();
        }else{
            $anggotas = Anggota::orderby('nama_anggota','asc')->select('kode_anggota','nama_anggota')->where('kode_anggota', $search)->limit(5)->where('status','aktif')->get();
        }
        $response = $anggotas->map(function ($anggota)
        {
            return [
                'id' => $anggota->kode_anggota,
                'text' => $anggota->nama_anggota
            ];
        });

        return response()->json($response,200);
    }

    public function searchId($id)
    {
        return Anggota::find($id);
    }

    public function getDetail(Request $request){
        $anggotaId = $request->anggotaId;

        $anggota = DB::table('t_anggota')
                ->join('t_penghasilan', 't_anggota.kode_anggota', 't_penghasilan.kode_anggota')
                ->join('t_kelas_company', 't_anggota.kelas_company_id', 't_kelas_company.id')
                ->select('t_anggota.kode_anggota', 't_anggota.nama_anggota', 't_penghasilan.value as gaji_bulanan', 't_kelas_company.nama', 't_kelas_company.id')
                ->where('t_anggota.kode_anggota', '=', $anggotaId)
                ->where('t_penghasilan.id_jenis_penghasilan', '=', 4)
                ->first();

        return response()->json($anggota, 200);
    }

    public function getKelasCompany(Request $request){
        $companyId = $request->companyId;
        $jenisAnggotaId = $request->jenisAnggotaId;

        $kelasCompany = KelasCompany::orderby('nama','asc')->select('id', 'nama')->where('company_id', $companyId)
                                            ->where('id_jenis_anggota', $jenisAnggotaId)->get();
        $response = $kelasCompany->map(function ($kelasComp)
        {
            return [
                'id' => $kelasComp->id,
                'text' => strtoupper($kelasComp->nama)
            ];
        });

        return response()->json($response, 200);
    }

    // Generate PDF
    public function createPDF(Request $request) {
        ini_set('max_execution_time', 120);
        $anggotas = Anggota::with('jenisAnggota');
        if ($request->status)
        {
            $anggotas = $anggotas->where('status', $request->status);
        }
        if ($request->id_jenis_anggota)
        {
            $anggotas = $anggotas->where('id_jenis_anggota', $request->id_jenis_anggota);
        }

        $anggotas = $anggotas->get();

        // share data to view
        view()->share('anggotas',$anggotas);
        $pdf = PDF::loadView('anggota.pdf', $anggotas)->setPaper('a4', 'landscape');

        // download PDF file with download method
        $filename = 'export_anggota_'.Carbon::now()->format('d M Y').'.pdf';
        return $pdf->download($filename);
    }

    public function createExcel(Request $request)
    {
        ini_set('max_execution_time', 300);
        $filename = 'export_anggota_excel_'.Carbon::now()->format('d M Y').'.xlsx';
        return Excel::download(new AnggotaExport($request), $filename);
    }

	public function importExcel()
	{
		$this->authorize('import anggota', Auth::user());
        $data['title'] = 'Import Anggota';
        return view('anggota.import', $data);
	}

	public function storeImportExcel(Request $request)
    {
        ini_set('max_execution_time', 300);
        $this->authorize('import anggota', Auth::user());
        try
        {
            Excel::import(new AnggotaImport, $request->file);
            return redirect()->back()->withSuccess('Import data berhasil');
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
            return redirect()->back()->withError('Gagal import data');
        }
	}
}
