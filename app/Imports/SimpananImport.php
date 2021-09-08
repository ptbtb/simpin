<?php

namespace App\Imports;

use App\Managers\JurnalManager;
use App\Managers\SimpananManager;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use App\Models\Code;
use App\Models\AngsuranSimpanan;
use Illuminate\Support\Facades\Log;
use DB;

class SimpananImport implements OnEachRow
{
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row = $row->toArray();
        if ($rowIndex == 1) {
            return null;
        }
        Log::info($rowIndex);
        $tglEntri = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[3])->format('Y-m-d');

        $periode = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4])->format('Y-m-d');
        $fields = [
            'jenis_simpan' => ($row[0] == "\N" || $row[0] == '' || $row[0] == null) ? '' : $row[0],
            'besar_simpanan' => ($row[1] == "\N" || $row[1] == '' || $row[1] == null) ? 0 : $row[1],
            'kode_anggota' => ($row[2] == "\N" || $row[2] == '' || $row[2] == null) ? '' : $row[2],
            'u_entry' => Auth::user()->name,
            'tgl_entri' => ($row[3] == "\N" || $row[3] == '' || $row[3] == null) ? null : Carbon::createFromFormat('Y-m-d', $tglEntri),
            'periode' => ($row[4] == "\N" || $row[4] == '' || $row[4] == null) ? null : Carbon::createFromFormat('Y-m-d', $periode),
            'kode_jenis_simpan' => ($row[5] == "\N" || $row[5] == '' || $row[5] == null) ? null : $row[5],
            'keterangan' => ($row[6] == "\N" || $row[6] == '' || $row[6] == null) ? null : $row[6],
            'id_akun_debet' => ($row[7] == "\N" || $row[7] == '' || $row[7] == null) ? null : $row[7],
        ];
        $nextSerialNumber = SimpananManager::getSerialNumber(Carbon::now()->format('d-m-Y'));
        $idakundebet=Code::where('CODE',$fields['id_akun_debet'])->first();
        if ($fields['kode_jenis_simpan'] == '411.01.000') {
            Log::info('SIMPANAN POKOK');
            $checkSimpanan = DB::table('t_simpan')->where('kode_anggota', '=', $fields['kode_anggota'] )->where('kode_jenis_simpan', '=', '411.01.000')->first();

            
                $simpanan = new Simpanan();
                $simpanan->jenis_simpan = strtoupper($fields['jenis_simpan'] );
                $simpanan->besar_simpanan = $fields['besar_simpanan'] ;
                $simpanan->kode_anggota = $fields['kode_anggota'] ;
                $simpanan->u_entry = Auth::user()->name;
                $simpanan->tgl_entri = $fields['tgl_entri'];
                $simpanan->tgl_transaki = $fields['tgl_entri'];
                $simpanan->kode_jenis_simpan = $fields['kode_jenis_simpan'] ;
                $simpanan->keterangan = ($fields['keterangan'] ) ? $fields['keterangan']  : null;
                $simpanan->id_akun_debet = ($idakundebet->id) ? $idakundebet->id : null;
                $simpanan->serial_number = $nextSerialNumber;
                $simpanan->save();
                JurnalManager::createJurnalSimpanan($simpanan);
                if ($fields['besar_simpanan']  < 499999) {
                    $existingSimpanan = DB::table('t_simpan')->where('kode_anggota', '=', $fields['kode_anggota'] )->where('kode_jenis_simpan', '=', '411.01.000')->first();

                    $indexAngsuran = DB::table('t_angsur_simpan')->where('kode_simpan', '=', $existingSimpanan->kode_simpan)->count();

                    $angsurSimpanan = new AngsuranSimpanan();
                    $angsurSimpanan->kode_simpan = $existingSimpanan->kode_simpan;
                    $angsurSimpanan->angsuran_ke = $indexAngsuran + 1;
                    $angsurSimpanan->besar_angsuran = $fields['besar_simpanan'] ;
                    $angsurSimpanan->kode_anggota = $fields['kode_anggota'] ;
                    $angsurSimpanan->u_entry = Auth::user()->name;
                    $angsurSimpanan->tgl_entri = $fields['tgl_entri'];
                    $angsurSimpanan->tgl_transaki = $fields['tgl_entri'];
                    $angsurSimpanan->created_at = Carbon::now();
                    $angsurSimpanan->updated_at = Carbon::now();
                    $angsurSimpanan->save();


                }

            
            Log::info('akhir SIMPANAN POKOK');
        }else {

            Log::info('BUKAN SIMPANAN POKOK');
            $periodeTime = $fields['periode'];
            $cek = Simpanan::where('kode_anggota',$fields['kode_anggota'])
            ->where('periode',$periode)
            ->where('kode_jenis_simpan',$fields['kode_jenis_simpan'])
            ->first();
            if ($cek){
                 $cek->jenis_simpan = strtoupper($fields['jenis_simpan']);
            $cek->besar_simpanan = $fields['besar_simpanan'];
            $cek->kode_anggota = $fields['kode_anggota'];
            $cek->u_entry = Auth::user()->name;
            $cek->tgl_entri = $fields['tgl_entri'];
            $cek->tgl_transaksi = $fields['tgl_entri'];
            $cek->periode = $periodeTime;
            $cek->kode_jenis_simpan = $fields['kode_jenis_simpan'];
            $cek->keterangan = ($fields['keterangan']) ? $fields['keterangan'] : null;
            $cek->id_akun_debet = ($idakundebet->id) ? $idakundebet->id : null;
            $cek->save();

            $journals = $cek->jurnals;
                foreach ($journals as $key => $journal)
                {
                    if($journal)
                    {
                        $journal->kredit = $cek->besar_simpanan;
                        $journal->debet = $cek->besar_simpanan;
                        $journal->updated_by = Auth::user()->id;
                        $journal->save();
                    }
                }
            }else{
            
            $simpanan = new Simpanan();
            $simpanan->jenis_simpan = strtoupper($fields['jenis_simpan']);
            $simpanan->besar_simpanan = $fields['besar_simpanan'];
            $simpanan->kode_anggota = $fields['kode_anggota'];
            $simpanan->u_entry = Auth::user()->name;
            $simpanan->tgl_entri = $fields['tgl_entri'];
            $simpanan->tgl_transaksi = $fields['tgl_entri'];
            $simpanan->periode = $periodeTime;
            $simpanan->kode_jenis_simpan = $fields['kode_jenis_simpan'];
            $simpanan->keterangan = ($fields['keterangan']) ? $fields['keterangan'] : null;
            $simpanan->id_akun_debet = ($idakundebet->id) ? $idakundebet->id : null;
            $simpanan->serial_number = $nextSerialNumber;
            $simpanan->save();
            JurnalManager::createJurnalSimpanan($simpanan);
            }
           
            Log::info('akhir BUKAN SIMPANAN POKOK');
        }

        
        return true;;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    /*public function model(array $row)
    {
        $col = explode(';',$row[0]);
        $fields = [
            'jenis_simpan' => ($col[0] == "\N" || $col[0] == '')? '':$col[0],
            'besar_simpanan' => ($col[1] == "\N" || $col[1] == '')? '':$col[1],
            'kode_anggota' => ($col[2] == "\N" || $col[2] == '')? '':$col[2],
            'u_entry' => ($col[3] == "\N" || $col[3] == '')? '':$col[3],
            'tgl_mulai' => ($col[4] == "\N" || $col[4] == '')? null:Carbon::createFromFormat('d/m/Y',$col[4]),
            'tgl_entri' => ($col[5] == "\N" || $col[5] == '')? '':Carbon::createFromFormat('d/m/Y',$col[5]),
            'kode_jenis_simpan' => ($col[6] == "\N" || $col[6] == '')? null:$col[6],
            'keterangan' => ($col[7] == "\N" || $col[7] == '')? null:$col[7],
        ];
        // dd($fields)
        // dump($fields);
        return new Simpanan($fields);
    }*/
}
