<?php

namespace App\Http\Middleware;

use App\Models\JenisPenghasilan;
use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PinjamanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user->isAnggota())
        {
            $anggota = $user->anggota;
            $listPenghasilan = $anggota->listPenghasilan;
            $data = [];
            if ($listPenghasilan)
            {
                $jenisPenghasilan = JenisPenghasilan::where('company_group_id', $anggota->company->company_group_id)
                                                    ->where('rule_name', 'gaji_bulanan')
                                                    ->first();

                $data = [
                    'kelas_company_id' => $anggota->kelas_company_id,
                    'gaji_bulanan' => ($listPenghasilan->where('id_jenis_penghasilan',$jenisPenghasilan->id)->first())? $listPenghasilan->where('id_jenis_penghasilan',$jenisPenghasilan->id)->first()->value:null,
                    'slip_gaji' => ($listPenghasilan->where('id_jenis_penghasilan',$jenisPenghasilan->id)->first())? $listPenghasilan->where('id_jenis_penghasilan',$jenisPenghasilan->id)->first()->file_path:null,
                    'foto_ktp' => $anggota->foto_ktp
                ];
            }
            $rule=[
                'kelas_company_id' => 'required',
                'gaji_bulanan' => 'required',
                'slip_gaji' => 'required',
                'foto_ktp' => 'required'
            ];
            $messages = [
                'gaji_bulanan.required' => 'Salary is required.',
                'foto_ktp.required' => 'KTP Photo is required.',
                'kelas_company_id.required' => 'Company class is required.',
                'slip_gaji.required' => 'Salary slip is required.'
            ];

            $validator = Validator::make($data, $rule, $messages);
            if ($validator->fails()) {
                // dd($validator->errors());
                return redirect()
                        ->route('user-profile')
                        ->withErrors($validator->errors());
            }
            return $next($request);
        }
        return $next($request);
    }
}
