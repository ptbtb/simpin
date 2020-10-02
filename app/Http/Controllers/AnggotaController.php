<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Exports\AnggotaExport;

use App\Models\Anggota;
use App\Models\JenisAnggota;

use Auth;
use Excel;
use PDF;
use Carbon\Carbon;

class AnggotaController extends Controller {

    public function index(Request $request)
    {
        $this->authorize('view anggota', Auth::user());
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
        $data['jenisAnggotas'] = JenisAnggota::all();
        return view('/anggota/create', $data);
    }

    public function edit($id) {
        $this->authorize('edit anggota', Auth::user());
        $anggota = Anggota::find($id);
        $data['title'] = 'Tambah Anggota';
        $data['anggota'] = $anggota;
        $data['jenisAnggotas'] = JenisAnggota::all();
        return view('/anggota/edit', $data);
    }

    public function store(Request $request) {
        $this->authorize('add anggota', Auth::user());
        try
        {
            DB::transaction(function () use ($request)
            {
                $Anggota = Anggota::create([
                    'kode_anggota' => $request->kode_anggota,
                    'kode_tabungan' => $request->kode_anggota,
                    'id_jenis_anggota' => $request->jensi_anggota,
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
                    'status' => 'aktif',
                ]);
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
            $Anggota->tgl_masuk = $request->tgl_masuk;
            $Anggota->nama_anggota = $request->nama_anggota;
            $Anggota->id_jenis_anggota = $request->jenis_anggota;
            $Anggota->tempat_lahir = $request->tmp_lahir;
            $Anggota->tgl_lahir = $request->tgl_lahir;
            $Anggota->alamat_anggota = $request->alamat_anggota;
            $Anggota->jenis_kelamin = $request->jenis_kelamin;
            $Anggota->telp = $request->telp;
            $Anggota->lokasi_kerja = $request->lokasi_kerja;
            $Anggota->u_entry = $request->u_entry;
            $Anggota->ktp = $request->ktp;
            $Anggota->nipp = $request->nipp;
            $Anggota->no_rek = $request->no_rek;
            $Anggota->email = $request->email;
            $Anggota->emergency_kontak = $request->emergency_kontak;
            $Anggota->status = 'aktif';
            $Anggota->save();
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
            $anggotas = Anggota::orderby('nama_anggota','asc')->select('kode_anggota','nama_anggota')->limit(5)->get();
        }else{
            $anggotas = Anggota::orderby('nama_anggota','asc')->select('kode_anggota','nama_anggota')->where('kode_anggota', $search)->limit(5)->get();
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

    // Generate PDF
    public function createPDF(Request $request) {
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
        $filename = 'export_anggota_excel_'.Carbon::now()->format('d M Y').'.xlsx';
        return Excel::download(new AnggotaExport($request), $filename);
    }
}
