<style>
    .logo{
        height: 120px;
        margin-right: 5%;
    }
    .text-center{
        text-align: center;
    }

    table tr td{
        font-size: 12px;
    }
    body{
        font-size: 12px;
    }
    h5{
        font-size: 12px;
    }
    .page-break {
    page-break-after: always;
}
</style>
<body>
    <div style="display: flex">
        <img src="{{ asset('img/new-logo.jpg') }}" class="logo">
        <div class="text-center">
            <h5 style="margin: 0">KOPERASI PEGAWAI MARITIM (KOPEGMAR)<br>TANJUNG PRIOK</h5>
            <p style="font-weight: normal; font-size: 11px;">
                Jl. Cempaka No 14 Tanjung Priok, Jakarta - 14230 <br>
                Phone : +62 21 - 439 30020, +62 21 - 430 2849 <br>
                Fax : +62 21 - 439 13776, e-mail : simpin@kopegmartanjungpriok.co.id <br>
                www.kopegmartanjungpriok.co.id
            </p>
        </div>
    </div>
    <div style="width: 200px; margin-left: auto; margin-top: -100px">
        <table>
            <tr>
                <td style="width: 50%">No</td>
                <td>:</td>
                <td style="width: 50%"></td>
            </tr>
            <tr>
                <td>Tanggal Diterima</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Diterima Oleh</td>
                <td>:</td>
                <td></td>
            </tr>
        </table>
    </div>
    <div class="text-center">
        <h5>FORMULIR PINJAMAN <br> {{ strtoupper($jenisPinjaman->kategoriJenisPinjaman->name) }}</h5>
        <table style="width: 100%">
            <tr>
                <td colspan="7">Yang bertanda tangan di bawah ini:</td>
            </tr>
            <tr>
                <td style="width: 15%">Nama</td>
                <td>:</td>
                <td style="width: 20%">{{ ucwords(strtolower($anggota->nama_anggota)) }}</td>
                <td style="width: 30%"></td>
                <td style="width: 15%">Tanggal Lahir</td>
                <td>:</td>
                <td style="width: 20%">{{ $anggota->tgl_lahir->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>NIPP</td>
                <td>:</td>
                <td>{{ $anggota->nipp }}</td>
                <td></td>
                <td>No KTP</td>
                <td>:</td>
                <td>{{ ($anggota->noKtp)? $anggota->noKtp:'.....................' }}</td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>:</td>
                <td>{{ ucwords(strtolower($anggota->company->nama)) }}</td>
                <td></td>
                <td>No Telp. / HP</td>
                <td>:</td>
                <td>{{ $anggota->telp }}</td>
            </tr>
            <tr>
                <td>No Anggota</td>
                <td>:</td>
                <td>{{ $anggota->kode_anggota }}</td>
                <td></td>
                <td>Email</td>
                <td>:</td>
                <td>{{ strtolower($anggota->email) }}</td>
            </tr>
            <tr>
                <td>Gaji Bersih</td>
                <td>:</td>
                <td id="gaji">{{ "Rp " . number_format($anggota->penghasilan->gaji_bulanan,2,',','.') }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
    <div>
        <ol>
            <li>
                Mengajukan pinjaman sebesar {{ "Rp " . number_format($besarPinjaman,2,',','.') }}. Terbilang : {{ $besarPinjamanTerbilang }} <br>
                Untuk Keperluan : {{ $keperluan }}<br>
                Jangka Waktu / Tenor Pinjaman : {{ $lamaAngsuran }} ( {{ $lamaAngsuranTerbilang }} ) bulan.
            </li>
            <li>
                Bersedia untuk ditransfer sebesar nominal yang disetujui oleh Kopegmar Tanjung Priok ke rekening atas nama pribadi pemohon sbb: <br>
                Nama pada rekening : {{ ucwords(strtolower($anggota->nama_anggota)) }}<br>
                Bank : Bank Mandiri<br>
                Nomor Rekening : {{ $anggota->no_rek }}<br>
            </li>
            <li>
                Memberikan kuasa pada Perusahaan tempat bekerja untuk melakukan pemotongan penghasilan <b>gaji bulanan</b> <br>
                Sesuai dengan angsuran pinjaman yang disetujui oleh Kopegmar Tanjung Priok.
            </li>
            <li>
                Setuju untuk dilakukan pemotongan atas penghasilannya terkait kewajiban yang timbul setelah pinjaman dicairkan oleh Kopegmar Tanjung Priok.
            </li>
        </ol>
        <table style="width: 100%">
            <tr>
                <td style="width: 25%; text-align: center">
                    Mengetahui <br>
                    Kepala ................................ <br><br><br><br><br>
                    (................................) <br>
                    NIPP:....................................................
                </td>
                <td style="width: 50%"></td>
                <td style="width: 25%; text-align: center">
                    Jakrta, {{ \Carbon\Carbon::now()->format('d/m/Y') }} <br>
                    Pemohon <br><br><br><br><br>
                    ( {{ ucwords(strtolower($anggota->nama_anggota)) }} ) <br>
                    NIPP: {{ $anggota->nipp }}
                </td>
            </tr>
        </table>
    </div>
    <hr>
    <h5></h5>
    <table style="width: 100%">
        <tr>
            <td colspan="4"><h5 style="text-decoration: underline; margin: 0">SALDO PINJAMAN</h5></td>
            <td colspan="4"><h5 style="text-decoration: underline; margin: 0">SALDO SIMPANAN</h5></td>
        </tr>
        <tr>
            <td>1.</td>
            <td style="width: 25%">Saldo Pinjaman Bank</td>
            <td>:</td>
            <td style="width: 25%">Rp ....................</td>
            <td>1.</td>
            <td style="width: 25%">Simpanan Pokok</td>
            <td>:</td>
            <td style="width: 25%">{{ "Rp " . number_format($anggota->tabungan->where('kode_trans',JENIS_SIMPANAN_POKOK)->first()->besar_tabungan,2,',','.') }}</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Saldo Pinjaman Gaji</td>
            <td>:</td>
            <td>Rp ....................</td>
            <td>2.</td>
            <td>Simpanan Wajib</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($anggota->tabungan->where('kode_trans',JENIS_SIMPANAN_WAJIB)->first()->besar_tabungan,2,',','.') }}</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>Saldo Pinjaman IP/Insentif</td>
            <td>:</td>
            <td>Rp ....................</td>
            <td>3.</td>
            <td>Simpanan Sukarela</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($anggota->tabungan->where('kode_trans',JENIS_SIMPANAN_SUKARELA)->first()->besar_tabungan,2,',','.') }}</td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Saldo Pinjaman Mobilitas</td>
            <td>:</td>
            <td>Rp ....................</td>
            <td>4.</td>
            <td>Simpanan Khusus</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($anggota->tabungan->where('kode_trans',JENIS_SIMPANAN_KHUSUS)->first()->besar_tabungan,2,',','.') }}</td>
        </tr>
        <tr>
            <td>5.</td>
            <td>Saldo Pinjaman Shift</td>
            <td>:</td>
            <td>Rp ....................</td>
            <td></td>
            <td>Jumlah Simpanan</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($anggota->tabungan->whereIn('kode_trans',[JENIS_SIMPANAN_KHUSUS,JENIS_SIMPANAN_SUKARELA,JENIS_SIMPANAN_POKOK,JENIS_SIMPANAN_WAJIB])->sum('besar_tabungan'),2,',','.') }}</td>
        </tr>
        <tr>
            <td>6.</td>
            <td>Saldo Pinjaman Transport</td>
            <td>:</td>
            <td>Rp ....................</td>
            <td>5.</td>
            <td>Kekurangan Pagu Simpanan</td>
            <td>:</td>
            <td>Rp ....................</td>
        </tr>
        <tr>
            <td>7.</td>
            <td>Saldo Pinjaman Fungsional</td>
            <td>:</td>
            <td>Rp ....................</td>
            <td>6.</td>
            <td>...............</td>
            <td>:</td>
            <td>Rp ....................</td>
        </tr>
        <tr>
            <td>8.</td>
            <td>Saldo Pinjaman Japen</td>
            <td>:</td>
            <td>Rp ....................</td>
            <td colspan="4"><h5 style="text-decoration: underline; margin: 0">BIAYA PINJAMAN</h5></td>
        </tr>
        <tr>
            <td>9.</td>
            <td>Saldo Kredit Barang</td>
            <td>:</td>
            <td>Rp ....................</td>
            <td>1.</td>
            <td>Asuransi</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($asuransi,2,',','.') }}</td>
        </tr>
        <tr>
            <td>10.</td>
            <td>Saldo Kredit Motor</td>
            <td>:</td>
            <td>Rp ....................</td>
            <td>2.</td>
            <td>Provisi</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($provisi,2,',','.') }}</td>
        </tr>
        <tr>
            <td>11.</td>
            <td>Saldo Japen "B"</td>
            <td>:</td>
            <td>Rp ....................</td>
            <td>3.</td>
            <td>Administrasi</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($biayaAdministrasi,2,',','.') }}<td>
        </tr>
        <tr>
            <td>12.</td>
            <td>Saldo Japan "B"</td>
            <td>:</td>
            <td>Rp ....................</td>
            <td>4.</td>
            <td>Angsuran Pokok</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($angsuranPokok,2,',','.') }}<td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>5.</td>
            <td>Jasa</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($jasa,2,',','.') }}</td>
        </tr>
    </table>
    {{-- <hr>
    <div class="page-break"></div>
    <h5 style="text-align: center">PERSETUJUAN PINJAMAN</h5>
    <p style="margin: 0">
        Permohonan saudara/I dapat disetujui dan diberikan pinjaman uang/barang atas permohonan tersebut sebesar <br>
        Rp ............................. Terbilang ( ..................................................................) <br>
        Jangka waktu/tenor pinjaman : {{ $lamaAngsuran }} ( {{ $lamaAngsuranTerbilang }} ) bulan <br>
        Yang akan dipotong dari penghasilan : ..................... <br>
    </p>
    <b>Rincian Angsuran ditetapkan</b> <br>
    <table style="width: 50%">
        <tr>
            <td style="width: 50%">Angsuran Pokok</td>
            <td>:</td>
            <td style="width: 50%">Rp..................</td>
        </tr>
        <tr>
            <td>Jasa Pinjaman</td>
            <td>:</td>
            <td style="text-decoration: underline">Rp..................</td>
        </tr>
        <tr>
            <td><b>Jumlah Potongan Per Bulan</b></td>
            <td>:</td>
            <td>Rp..................</td>
        </tr>
        <tr>
            <td colspan="3"><b style="text-decoration: underline"Jumlah Yang Harus Dibayarkan</b></td>
        </tr>
        <tr>
            <td>Pinjaman</td>
            <td>:</td>
            <td>Rp..........</td>
        </tr>
        <tr>
            <td>Potongan</td>
            <td>:</td>
            <td style="text-decoration: underline">Rp..........</td>
        </tr>
        <tr>
            <td>Jumlah Uang Diterima Sebesar</td>
            <td>:</td>
            <td>Rp..........</td>
        </tr>
    </table>
    <hr>
    <table style="width: 100%">
        <tr>
            <td style="width: 50%">
                <table style="width: 90%; margin-left: 5%; margin-right: 5; border: 1px solid #000000; padding-left: 5%; padding-right: 5%">
                    <tr>
                        <td colspan="2">Hanya diisi jika pembayaran pinjaman dilakukan melalui kasir</td>
                    </tr>
                    <tr>
                        <td style="width: 50%; text-align: center">
                            Kasir <br><br><br><br><br><br>
                            (............................)
                        </td>
                        <td style="width: 50%; text-align: center">
                            Penerima <br><br><br><br><br><br>
                            (............................)
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; text-align: center">
                Jakarta, {{ \Carbon\Carbon::now()->format('d/m/Y') }} <br>
                Pember Persetujuan <br><br><br><br><br><br><br>
                (..................................)
            </td>
        </tr>
    </table> --}}
    <br>
    * Lampirkan FC KTP, SLIP Gaji, IP dan Jaminan untuk Pinjaman Jangka Pendek <br>
    
</body>