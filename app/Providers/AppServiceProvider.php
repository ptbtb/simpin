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
        if ($this->app->environment('local')) {
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
                $allNotif = Notification::all()->where('receiver', Auth::user()->id)
                                                ->where('role_id', $role->id)
                                                ->where('has_read', 0);
                $notification['all_notification'] = $allNotif;
                $notification['count'] = $allNotif->count();
                $view->with('notification', $notification );
            }
        }); 
    }
}
