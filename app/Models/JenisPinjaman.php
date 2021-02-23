<?php

namespace App\Models;

use App\Models\View\ViewSaldo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class JenisPinjaman extends Model
{
    use HasFactory;

    protected $table = "t_jenis_pinjam";
    protected $primaryKey = "kode_jenis_pinjam";
    protected $keyType = 'string';
    public $incrementing = false;
    protected $dates = ['tgl_entry'];
    public $timestamps = false;
    protected $fillable= [
        'kode_jenis_pinjam',
        'tipe_jenis_pinjaman_id',
        'kategori_jenis_pinjaman_id',
        'nama_pinjaman',
        'lama_angsuran',
        'maks_pinjam',
        'bunga',
        'asuransi',
        'biaya_admin',
        'provisi',
        'jasa',
        'jasa_pelunasan_dipercepat',
        'minimal_angsur_pelunasan',
        'u_entry',
        'tgl_entri'
    ];

    public function kategoriJenisPinjaman()
    {
        return $this->belongsTo(KategoriJenisPinjaman::class, 'kategori_jenis_pinjaman_id');
    }

    public function tipeJenisPinjaman()
    {
        return $this->belongsTo(TipeJenisPinjaman::class, 'tipe_jenis_pinjaman_id');
    }

    public function listPinjaman()
    {
        return $this->hasMany(Pinjaman::class,'kode_jenis_pinjam');
    }

    public function isDanaKopegmar()
    {
        return $this->tipe_jenis_pinjaman_id == TIPE_JENIS_PINJAMAN_DANA_KOPEGMAR;
    }

    public function isDanaLain()
    {
        return $this->tipe_jenis_pinjaman_id == TIPE_JENIS_PINJAMAN_DANA_LAIN;
    }

    public function isJangkaPendek()
    {
        return $this->kategori_jenis_pinjaman_id == KATEGORI_JENIS_PINJAMAN_JANGKA_PENDEK;
    }

    public function isJangkaPanjang()
    {
        return $this->kategori_jenis_pinjaman_id == KATEGORI_JENIS_PINJAMAN_JANGKA_PANJANG;
    }

    public function scopeJapan($query)
    {
        return $query->where('kategori_jenis_pinjaman_id', KATEGORI_JENIS_PINJAMAN_JANGKA_PANJANG);
    }

    public function maxPinjaman(User $user)
    {
        $jenisPinjaman = $this;
        $anggota = $user->anggota;
        if(is_null($anggota))
        {
            return 0;
        }

        if ($jenisPinjaman->isJangkaPanjang()) {
            if ($anggota->isPensiunan()) {
                $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                return $saldo->jumlah * 0.75;
            } elseif ($anggota->isAnggotaBiasa()) {
                if ($jenisPinjaman->isDanaKopegmar()) {
                    $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                    return $saldo->jumlah * 5;
                } elseif ($jenisPinjaman->isDanaLain()) {
                    $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                    return $saldo->jumlah * 8;
                }
            } elseif ($anggota->isAnggotaLuarBiasa()) {
                $company = $anggota->company;
                if ($company->isKopegmarGroup()) {
                    return 30000000;
                }
                if ($company->isKojaGroup()) {
                    $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                    return $saldo->jumlah * 5;
                }
            }
        } elseif ($jenisPinjaman->isJangkaPendek()) {
            $penghasilanTertentu = Penghasilan::where('kode_anggota', $anggota->kode_anggota)
                    ->penghasilanTertentu()
                    ->get();
            if (!$penghasilanTertentu->count()) {
                return response()->json(['message' => 'Tidak memiliki penghasilan tertentu'], 412);
            }
            
            if ($anggota->isAnggotaBiasa()) {
                return 100000000;
            } elseif ($anggota->isAnggotaLuarBiasa()) {
                return 100000000;
            } elseif ($anggota->isPensiunan()) {
                $saldo = ViewSaldo::where('kode_anggota', $anggota->kode_anggota)->first();
                return $saldo->jumlah * 0.75;
            }
        }
        return 0;
    }
}
