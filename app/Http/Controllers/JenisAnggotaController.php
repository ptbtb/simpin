<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\JenisAnggota;

use Auth;
use DB;

class JenisAnggotaController extends Controller
{
    public function index()
    {
        $this->authorize('view jenis anggota', Auth::user());
        $jenisAnggotas = JenisAnggota::with('createdBy','updatedBy')->get();
        $data['title'] = 'List Jenis Anggota';
        $data['jenisAnggotas'] = $jenisAnggotas;
        return view('jenis_anggota.index', $data);
    }

    public function create()
    {
        $this->authorize('add jenis anggota', Auth::user());
        $data['title'] = 'Tambah Jenis Anggota';
        return view('jenis_anggota.create', $data);
    }

    public function store(Request $request)
    {
        try
        {
            $this->authorize('add jenis anggota', Auth::user());
            DB::transaction(function () use ($request)
            {
                $user = Auth::user();
                $jenisAnggota = new JenisAnggota();
                $jenisAnggota->code_jenis_anggota = $request->kode_jenis_anggota;
                $jenisAnggota->nama_jenis_anggota = $request->nama_jenis_anggota;
                $jenisAnggota->prefix = $request->prefix;
                $jenisAnggota->create_by = $user->id;
                $jenisAnggota->update_by = $user->id;
                $jenisAnggota->save(); 
            });

            return redirect()->route('jenis-anggota-list')->withSuccess('Tambah jenis anggota berhasil');
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

    public function edit($id)
    {
        $this->authorize('edit jenis anggota', Auth::user());
        $jenisAnggota = JenisAnggota::findOrFail($id);
        $data['title'] = 'Edit Jenis Anggota';
        $data['jenisAnggota'] = $jenisAnggota;
        return view('jenis_anggota.edit', $data);
    }

    public function update($id, Request $request)
    {
        try
        {
            $this->authorize('edit jenis anggota', Auth::user());
            $jenisAnggota = JenisAnggota::findOrFail($id);
            DB::transaction(function () use ($request, $jenisAnggota)
            {
                $user = Auth::user();
                $jenisAnggota->code_jenis_anggota = $request->kode_jenis_anggota;
                $jenisAnggota->nama_jenis_anggota = $request->nama_jenis_anggota;
                $jenisAnggota->prefix = $request->prefix;
                $jenisAnggota->create_by = $user->id;
                $jenisAnggota->update_by = $user->id;
                $jenisAnggota->save(); 
            });

            return redirect()->route('jenis-anggota-list')->withSuccess('Ubah jenis anggota berhasil');
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

    public function delete($id)
    {
        try
        {
            $this->authorize('delete jenis anggota', Auth::user());
            $jenisAnggota = JenisAnggota::findOrFail($id);
            $jenisAnggota->delete();
            return redirect()->back()->withSuccess('Hapus jenis anggota berhasil');
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
}
