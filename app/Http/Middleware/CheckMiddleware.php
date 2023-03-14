<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanySetting;

class CheckMiddleware
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
        $maintenance= CompanySetting::findorfail(7)->value;
        if($maintenance==1){
            if(!Auth::user()->hasRole('Admin')){
                Auth::logout();
                return redirect()->route('login')->withError('Aplikasi sedang dalam Pemeliharaan');
            }
        }

        if (Auth::user()->isVerified())
        {
             if (Auth::user()->isAllowed())
        {
            return $next($request);
        }

        }
        Auth::logout();
        return redirect()->route('login')->withError('Akun anda belum di verifikasi');
    }
}
