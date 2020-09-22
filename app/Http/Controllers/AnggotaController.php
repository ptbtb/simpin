<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Anggota;

class AnggotaController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $anggotas = \App\Models\Anggota::with('jenisAnggota')->where('status', 'aktif')->orderBy('kode_anggota', 'desc')->get();
        $data['anggota'] = $anggotas;
        $data['judul'] = 'Anggota Aktif';
        return view('/anggota/record', ['data' => $data]);
    }

    public function nonaktif() {
        $anggotas = \App\Models\Anggota::with('jenisAnggota')->where('status', 'keluar')->orderBy('kode_anggota', 'desc')->get();
        $data['anggota'] = $anggotas;
         $data['judul'] = 'Anggota Non Aktif';
        return view('/anggota/record', ['data' => $data]);
    }

    public function all() {
        $anggotas = \App\Models\Anggota::with('jenisAnggota')->orderBy('kode_anggota', 'desc')->get();
        $data['anggota'] = $anggotas;
         $data['judul'] = 'Semua Anggotta';
        return view('/anggota/record', ['data' => $data]);
    }

    public function add() {
        $nomer = \App\Models\Anggota::max('kode_anggota');
        return view('/anggota/add', ['nomer' => $nomer + 1]);
    }

    public function edit($id) {
        $anggota = \App\Models\Anggota::find($id);
        return view('/anggota/edit', ['anggota' => $anggota]);
    }

    public function store(Request $request) {

        $Anggota = \App\Models\Anggota::create([
                    'kode_anggota' => $request->kode_anggota,
                    'kode_tabungan' => $request->kode_anggota,
                    'tgl_masuk' => $request->tgl_masuk,
                    'nama_anggota' => $request->nama_anggota,
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
        // alihkan halaman tambah buku ke halaman books
        return redirect('/anggota')->with('status', 'Data anggota Berhasil Ditambahkan');
    }

    public function update(Request $request, $id) {
        $Anggota = \App\Models\Anggota::find($id);
        $Anggota->tgl_masuk = $request->tgl_masuk;
        $Anggota->nama_anggota = $request->nama_anggota;
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
        $Anggota->save();
        // alihkan halaman tambah buku ke halaman books
        return redirect('/anggota')->with('status', 'Data anggota Berhasil Dirubah');
    }

    public function destroy($ids) {
        $Anggota = \App\Models\Anggota::destroy($ids);
       
        return redirect('/anggota')->with('status', 'Data Berhasil Dihapus');
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
            $anggotas = Anggota::orderby('nama_anggota','asc')->select('kode_anggota','nama_anggota')->where('nama_anggota', 'like', '%' .$search . '%')->limit(5)->get();
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
}
