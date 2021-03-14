<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'kode_anggota', 'created_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Validate the password of the user for the Passport password grant.
     *
     * @param  string  $password
     * @return bool
     */
    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($password, $this->password);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function getPhotoProfileAttribute()
    {
        if ($this->photo_profile_path)
        {
            return env('APP_URL').'/'.$this->photo_profile_path;
        }

        return env('APP_URL').'/img/user.jpg';
    }

    public function adminlte_image()
    {
        return $this->photoProfile;
    }

    public function adminlte_desc()
    {
        return $this->email;
    }

    public function adminlte_profile_url()
    {
        return 'user/profile';
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota', 'kode_anggota');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isAnggota()
    {
        return $this->roles->first()->id == ROLE_ANGGOTA;
    }

    public function isAdmin()
    {
        return $this->roles->first()->id == ROLE_ADMIN;
    }

    public function isVerified()
    {
        return $this->is_verified == 1;
    }

    public function scopeOperatorSimpin($query)
    {
        return $query->whereHas('roles', function ($q)
        {
            return $q->where('id', ROLE_OPERATOR_SIMPIN);
        });
    }

    public function scopeSpv($query)
    {
        return $query->whereHas('roles', function ($q)
        {
            return $q->where('id', ROLE_SPV);
        });
    }

    public function scopeAsman($query)
    {
        return $query->whereHas('roles', function ($q)
        {
            return $q->where('id', ROLE_ASMAN);
        });
    }

    public function scopeManager($query)
    {
        return $query->whereHas('roles', function ($q)
        {
            return $q->where('id', ROLE_MANAGER);
        });
    }

    public function scopeBendahara($query)
    {
        return $query->whereHas('roles', function ($q)
        {
            return $q->where('id', ROLE_BENDAHARA);
        });
    }

    public function scopeKetua($query)
    {
        return $query->whereHas('roles', function ($q)
        {
            return $q->where('id', ROLE_KETUA);
        });
    }
}
