<?php

namespace App\Imports;

use App\Events\Anggota\AnggotaCreated;
use App\Models\Anggota;
use App\Models\Bank;
use App\Models\JenisAnggota;
use App\Models\KelasCompany;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class AnggotaImport implements OnEachRow
{
    /**
    * @param Collection $collection
    */
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        
        if ($rowIndex == 1 || is_null($row[0]))
        {
            return null;
        }
        
        $kode_anggota = $row[0];
        $kode_tabungan = $row[1];
        $user = Auth::user();
        $status = $row[17];
        $tgl_lahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[6])->format('Y-m-d');
        $tgl_masuk = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[12])->format('Y-m-d');
        $jenis_anggota= JenisAnggota::where('nama_jenis_anggota',trim($row[2]))->first();
        if($jenis_anggota){
            $id_jenis_anggota= $jenis_anggota->id_jenis_anggota;
        }else{
             $id_jenis_anggota= null;
        }

        

        $company = Company::where('nama',trim($row[10]))->first();
        if ($company){
            $company_id=$company->id;
        }else{
             $company_id=null;
        }
        $kelas_company = KelasCompany::where('nama',trim($row[11]))
            ->where('id_jenis_anggota',$id_jenis_anggota)
            ->where('company_id',$company_id)
            ->first();
        if ($kelas_company){
            $kelas_company_id=$kelas_company->id;
        }else{
            $kelas_company_id=null;
        }
        // $idbank = Bank::where('kode', trim($row[18]))->first();
        // dd($row[2]);
        $fields = [
            'kode_anggota' => $kode_anggota,
            // 'kode_tabungan' => $kode_tabungan,
            'id_jenis_anggota' => $id_jenis_anggota,
            'nipp' =>  $row[3],
            'nama_anggota' => $row[4],
            'tempat_lahir' => $row[5],
            'tgl_lahir' => Carbon::createFromFormat('Y-m-d', $tgl_lahir),
            'jenis_kelamin' => $row[7],
            'alamat_anggota' => $row[8],
            'ktp' => $row[9],
            'company_id' => $company_id,
            'kelas_company_id' => $kelas_company_id,
            'tgl_masuk' => Carbon::createFromFormat('Y-m-d', $tgl_masuk),
            'email' => $row[13],
            'telp' => $row[14],
            'emergency_kontak' => $row[15],
            'no_rek' => $row[16],
            'u_entry' => $user->id,
            // 'id_bank' => $idbank->id,
            'status' => $status
        ];

        // \Log::info(collect($fields));
        
        // if email or nipp is excist, next
        $anggota = Anggota::where('kode_anggota', $fields['kode_anggota'])
        ->first();

        if ($anggota)
        {
            $anggota->update($fields);
        }else{
            $anggota = Anggota::create($fields);
            $password =  Hash::make(uniqid());
            event(new AnggotaCreated($anggota,$password));
        }

        
    }
}
