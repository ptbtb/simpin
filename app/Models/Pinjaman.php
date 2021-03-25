<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class Pinjaman extends Model {

    use HasFactory;
    use Userstamps;

    protected $table = "t_pinjam";
    protected $primaryKey = "id";
    protected $keyType = 'string';
    public $incrementing = false;
    protected $dates = ['tgl_entri', 'tgl_tempo'];

    public function anggota() {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    public function jenisPinjaman() {
        return $this->belongsTo(JenisPinjaman::class, 'kode_jenis_pinjam');
    }

    public function pengajuan() {
        return $this->belongsTo(Pengajuan::class, 'kode_pengajuan_pinjaman');
    }

    public function statusPinjaman() {
        return $this->belongsTo(StatusPinjaman::class, 'id_status_pinjaman');
    }

    public function listAngsuran() {
        return $this->hasMany(Angsuran::class, 'kode_pinjam');
    }

    public function akunDebet()
    {
        return $this->belongsTo(Code::class, 'id_akun_debet');
    }

    /**
     * Get all of the pinjaman's jurnals.
    */
    public function jurnals()
    {
        return $this->morphMany(Jurnal::class, 'jurnalable');
    }

    public function scopeNotPaid($query) {
        return $query->where('id_status_pinjaman', STATUS_PINJAMAN_BELUM_LUNAS);
    }

    public function scopePaid($query) {
        return $query->where('id_status_pinjaman', STATUS_PINJAMAN_LUNAS);
    }

    public function scopeJapan($query)
    {
        return $query->whereHas('jenisPinjaman', function ($q)
        {
            return $q->japan();
        });
    }

    public function getPinjamanDiTransferAttribute() {
        return $this->besar_pinjam - $this->biaya_administrasi - $this->biaya_provisi - $this->biaya_asuransi - $this->biaya_jasa - $this->totalPinjamanTopup;
    }

    public function getTotalPinjamanTopupAttribute()
    {
        return $this->pengajuan->pengajuanTopup->sum('biaya_pelunasan_dipercepat');
    }

    public function getLamaAngsuranBelumLunasAttribute() {
        return $this->listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS)->count();
    }

    public function getTotalDendaAttribute() {
        return $this->listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS)->sum('denda');
    }

    public function getTotalAngsuranAttribute() {
        return $this->listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS)->sum('besar_angsuran');
    }

    public function getJasaPelunasanDipercepatAttribute() {
        return $this->besar_pinjam * $this->jenisPinjaman->jasa_pelunasan_dipercepat;
    }

    public function getTotalbayarPelunasanDipercepatAttribute()
    {
        return $this->totalAngsuran + $this->totalDenda + $this->jasaPelunasanDipercepat + $this->tunggakan;
    }

    public function getTunggakanAttribute() {
        // ambil tunggakan angsuran
        $tunggakan = $this->listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS)->where('besar_pembayaran', '>', 0)->first();
        if ($tunggakan)
        {            
            return $tunggakan->besar_angsuran + $tunggakan->jasa - $tunggakan->besar_pembayaran;
        }
        return 0;
    }

    public function getAngsuranBulanIniAttribute()
    {
        return $this->listAngsuran->filter(function ($angsuran)
        {
            return $angsuran->jatuh_tempo->format('m') == Carbon::now()->format('m');
        })->first();
    }

    public function getListTunggakanAngsuranAttribute()
    {
        $tunggakan = $this->listAngsuran
                        ->where('id_status_angsuran', STATUS_ANGSURAN_BELUM_LUNAS)
                        ->where('besar_pembayaran', '>', 0)
                        ->values();
        
        return $tunggakan;
    }

    public function canPercepatPelunasan()
    {
        $minimalAngsuranLunas = $this->minimal_angsur_pelunasan;
        $angsuranLunas = $this->listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_LUNAS)->count();
        return $angsuranLunas >= $minimalAngsuranLunas;
    }
}
