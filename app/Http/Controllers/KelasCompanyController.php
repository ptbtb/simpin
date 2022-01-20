<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyGroup;
use App\Models\KelasCompany;
use App\Models\JenisAnggota;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KelasCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
      $company =  Company::findOrFail($id);
      $list = $company->kelasCompany;
      $data['company'] = $company;
      $data['list'] = $list;
      $data['title'] = 'List Kelas '.$company->nama;

      return view('company.kelas.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
      $company = Company::findOrFail($id);
      $jenisAnggota = JenisAnggota::pluck('nama_jenis_anggota', 'id_jenis_anggota');
      $data['company'] = $company;
      $data['jenisAnggota'] = $jenisAnggota;
      return view('company.kelas.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
      try
      {
          $company = Company::find($id);
          if (is_null($company))
          {
              throw new \Exception ('Company  Tidak Ditemukan');
          }

          DB::transaction(function () use($company, $request)
          {
              $kelas = new KelasCompany();
              $kelas->id_jenis_anggota = $request->id_jenis_anggota;
              $kelas->nama = $request->nama;
              $kelas->sequence = $request->sequence;
              $kelas->company_id = $company->id;
              $kelas->save();
          });

          return redirect()->back()->withSuccess('Berhasil update data');
      }
      catch (\Throwable $th)
      {
          $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
          Log::error($message);
          return redirect()->back()->withErrors($message);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $kelas = KelasCompany::findOrFail($id);
      $jenisAnggota = JenisAnggota::pluck('nama_jenis_anggota', 'id_jenis_anggota');
      $data['kelas'] = $kelas;
      $data['jenisAnggota'] = $jenisAnggota;
      return view('company.kelas.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      try
      {
          $kelas = KelasCompany::find($id);
          if (is_null($kelas))
          {
              throw new \Exception ('Kelas Tidak Ditemukan');
          }

          DB::transaction(function () use($kelas, $request)
          {
              $kelas->id_jenis_anggota = $request->id_jenis_anggota;
              $kelas->nama = $request->nama;
              $kelas->sequence = $request->sequence;
              $kelas->save();
          });

          return redirect()->back()->withSuccess('Berhasil update data');
      }
      catch (\Throwable $th)
      {
          $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
          Log::error($message);
          return redirect()->back()->withErrors($message);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
