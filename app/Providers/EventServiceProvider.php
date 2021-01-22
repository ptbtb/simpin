<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\User\UserCreated' => [
            // 'App\Listeners\EmailListener@onUserCreated',
        ],
        'App\Events\Penarikan\PenarikanCreated' => [
            'App\Listeners\PenarikanListener@onPenarikanCreated',
        ],
        'App\Events\Pinjaman\PengajuanCreated' => [
            // 'App\Listeners\EmailListener@onPengajuanCreated',
        ],
        'App\Events\Pinjaman\PengajuanUpdated' => [
            // 'App\Listeners\EmailListener@onPengajuanUpdated',
        ],
        'App\Events\Pinjaman\PengajuanApproved' => [
            'App\Listeners\PinjamanListener@onPengajuanApproved',
            // 'App\Listeners\EmailListener@onPengajuanApproved',
        ],
        'App\Events\Pinjaman\PinjamanCreated' => [
            'App\Listeners\AngsuranListener@onPinjamanCreated',
        ],
        'App\Events\Anggota\AnggotaCreated' => [
            'App\Listeners\TabunganListener@onAnggotaCreated',
            'App\Listeners\UserListener@onAnggotaCreated',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
