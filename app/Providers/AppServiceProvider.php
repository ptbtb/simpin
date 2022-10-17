<?php

namespace App\Providers;
use App\Models\Notification;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local') || $this->app->environment('production')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // $notification = Notification::all();
        // $notification = Auth::user()->isAdmin();
        // View::share('allNotification', $notification);

        //compose all the views....
        View::composer('*', function ($view)
        {
            if(Auth::user())
            {
                $role = Auth::user()->roles->first();

                /**
                 * code ini bakal mengambil semua notifikasi lalu di filter. berarti select semua row.
                 * dan itu sangat berat di query.
                 */

                /*$allNotif = Notification::all()->where('receiver', Auth::user()->id)
                                                ->where('role_id', $role->id)
                                                ->where('has_read', 0);*/

                /**
                 * 26/03/2022 Arya Praza M
                 * mengatasi error ketika user tidak punya role
                 */
                if(is_null($role))
                {  
                    $user = Auth::user(); 
                    $user->assignRole('Anggota');
                    $user->save();
                    $role = $user->roles->first();
                }

                
                /**
                 * saya update dengan code seperti ini
                 */

                /* $allNotif = Notification::where('receiver', Auth::user()->id)
                                        ->where('role_id', $role->id)
                                        ->where('has_read', 0)
                                        ->get(); */
                $allNotif = collect([]);
                $notification['all_notification'] = $allNotif;
                $notification['count'] = $allNotif->count();
                $view->with('notification', $notification );
            }
        });
    }
}
