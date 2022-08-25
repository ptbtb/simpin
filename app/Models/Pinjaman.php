<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;
use OwenIt\Auditing\Contracts\Auditable;

class Pinjaman extends Model implements Auditable
{

    use HasFactory;
    use Userstamps;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = "t_pinjam";
    protected $primaryKey = "id";
    // protected $keyType = 'string';
    // public $incrementing = false;
    protected $dates = ['tgl_entri', 'tgl_tempo',];
    protected $appends = ['serial_number_view','serial_number_kredit_view'];
    protected $fillable = ['kode_anggota','kode_jenis_pinjam','besar_pinjam','sisa_pinjaman','biaya_asuransi','biaya_provisi','biaya_administrasi','id_status_pinjaman'];

    public function anggota() {
        return $this->belongsTo(Anggota::class, 'kode_anggota');
    }

    public function jenisPinjaman() {
        return $this->belongsTo(JenisPinjaman::class, 'kode_jenis_pinjam');
    }

    public function pengajuan() {
        return $this->belongsTo(Pengajuan::class, 'kode_pengajuan_pinjaman','kode_pengajuan');
    }

    public function statusPinjaman() {
        return $this->belongsTo(StatusPinjaman::class, 'id_status_pinjaman');
    }

    public function listAngsuran() {
        return $this->hasMany(Angsuran::class, 'kode_pinjam', 'kode_pinjam');
    }

    public function akunDebet()
    {
        return $this->belongsTo(Code::class, 'id_akun_debet');
    }

    public function akunKredit()
    {
        return $this->belongsTo(Code::class, 'id_akun_kredit');
    }

    /**
     * Get all of the pinjaman's jurnals.
    */
    public function jurnals()
    {
        return $this->morphMany(Jurnal::class, 'jurnalable');
    }

    public function PinjamanRestrukturisasi()
    {
        return $this->hasMany(PinjamanRestruktur::class, 'kode_pinjam');
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

    public function scopeJapen($query)
    {
        return $query->whereHas('jenisPinjaman', function ($q)
        {
            return $q->japen();
        });
    }

    public function getPinjamanDiTransferAttribute() {
        return $this->besar_pinjam - $this->biaya_administrasi - $this->biaya_provisi - $this->biaya_asuransi - $this->totalPinjamanTopup - $this->pengajuan->transfer_simpanan_pagu - $this->biaya_jasa_topup;
    }

    public function getTotalPinjamanTopupAttribute()
    {
        return $this->pengajuan->pengajuanTopup->sum('total_bayar_pelunasan_dipercepat');
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
        // return $this->besar_pinjam * $this->jenisPinjaman->jasa_pelunasan_dipercepat;
        return $this->service_fee;
        // return $this->sisa_pinjaman * $this->jenisPinjaman->jasa_pelunasan_dipercepat - $this->total_diskon;
    }
    public function getJasaTopup() {
        // return $this->besar_pinjam * $this->jenisPinjaman->jasa_pelunasan_dipercepat;
        // return $this->sisa_pinjaman * $this->jenisPinjaman->jasa_topup - $this->total_diskon;
        return $this->biaya_jasa_topup;
    }

    public function getTotalbayarPelunasanDipercepatAttribute()
    {
        return $this->sisa_pinjaman + $this->totalDenda + $this->jasaPelunasanDipercepat + $this->tunggakan;
    }
    public function getTotalbayarTopupAttribute()
    {
        return $this->sisa_pinjaman + $this->totalDenda + $this->JasaTopup + $this->tunggakan;
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
        // return true;
        $minimalAngsuranLunas = $this->minimal_angsur_pelunasan;
        $angsuranLunas = $this->listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_LUNAS)->count();
        return $angsuranLunas >= $minimalAngsuranLunas;
    }

    public function getSerialNumberViewAttribute()
    {
        if ($this->tgl_pelunasan && $this->serial_number)
        {
            return 'PCP' . Carbon::createFromFormat('Y-m-d',$this->tgl_pelunasan)->format('Y') . Carbon::createFromFormat('Y-m-d',$this->tgl_pelunasan)->format('m') . str_pad($this->serial_number, 4, "0", STR_PAD_LEFT);
        }
        return '-';
    }

    public function getSerialNumberKreditViewAttribute()
    {
        if ($this->tgl_transaksi && $this->serial_number_kredit)
        {
            return 'PIJ' . Carbon::createFromFormat('Y-m-d',$this->tgl_transaksi)->format('Y') . Carbon::createFromFormat('Y-m-d',$this->tgl_transaksi)->format('m') . str_pad($this->serial_number_kredit, 4, "0", STR_PAD_LEFT);
        }
        return '-';
    }

    public function getTotalDiscountAttribute()
    {
        return $this->diskon/100*$this->biaya_jasa;
    }

    /**
     * Get the bulan tagihan
     *
     * @param  string  $value
     * @return string
     */
    public function getTagihanBulanAttribute()
    {
        $angsuranLunas = $this->lama_angsuran - $this->sisa_angsuran;

        $addMonth = 1;
        if($angsuranLunas)
        {
            $addMonth = $angsuranLunas + 1;
        }

        return $this->tgl_entri->addMonths($addMonth)->endOfMonth();
    }

    /**
     * Get the angsuran sekarang
     *
     * @param  string  $value
     * @return string
     */
    public function getAngsuranSekarangAttribute($value)
    {
        return $this->lama_angsuran - $this->sisa_angsuran + 1;
    }

    public function isPaidOff()
    {
        if($this->besar_angsuran <= $this->listAngsuran->where('id_status_angsuran', STATUS_ANGSURAN_LUNAS)->count('besar_angsuran'))
        {
            return true;
        }
        return false;
    }
}
