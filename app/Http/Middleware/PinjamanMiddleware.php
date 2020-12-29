<?php

namespace App\Http\Middleware;

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
            $penghasilan = $anggota->penghasilan;
            $data = [];
            if ($penghasilan)
            {
                $data = $penghasilan->toArray();
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
