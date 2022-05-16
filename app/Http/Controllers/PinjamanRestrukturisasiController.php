<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use App\Models\PinjamanRestruktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PinjamanRestrukturisasiController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try
        {
            $pinjaman = Pinjaman::where('kode_pinjam', $request->kode_pinjam)
                                ->first();

            if(is_null($pinjaman))
            {
                return redirect()->back()->withErrors("Pinjaman Tidak Ditemukan");
            }

            // dd($pinjaman, $request);
            // create restruktur
            $restruktur = new PinjamanRestruktur();
            $restruktur->kode_pinjam = $pinjaman->kode_pinjam;
            $restruktur->old_tenor = $pinjaman->sisa_angsuran;
            $restruktur->old_angsuran = $pinjaman->sisa_pinjaman;
            $restruktur->new_tenor = $request->new_tenor;
            $restruktur->new_angsuran = round($pinjaman->sisa_pinjaman/$request->new_tenor);
            $restruktur->created_by = Auth::user()->id;
            $restruktur->save();

            $file = $request->dokumen_persetujuan;
			if ($file)
			{
				$config['disk'] = 'upload';
				$config['upload_path'] = '/pinjaman/'.$pinjaman->id.'/restrukturisasi/'.$restruktur->id;
				$config['public_path'] = env('APP_URL') . $config['upload_path'];

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
					$restruktur->dokumen_persetujuan = $config['disk'].$config['upload_path'].'/'.$filename;
                    $restruktur->save();
				}
			}

            self::updatePinjaman($pinjaman, $restruktur);

            // dd($restruktur);
            DB::commit();

            return redirect()->back()->withSuccess('Berhasil mengajukan restrukturisasi');
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            DB::rollBack();
            dd($th);
            return redirect()->back()->withErrors($message);
        }
    }
    
    public static function updatePinjaman(Pinjaman $pinjaman, PinjamanRestruktur $restuktur)
    {
        $pinjaman->lama_angsuran = $restuktur->new_tenor;
        $pinjaman->sisa_angsuran = $restuktur->new_tenor;
        $selisih = $pinjaman->besar_angsuran - $pinjaman->besar_angsuran_pokok;
        $pinjaman->besar_angsuran = $restuktur->new_angsuran;
        $pinjaman->besar_angsuran_pokok = $restuktur->new_angsuran-$selisih;
        $pinjaman->save();
    }
}
