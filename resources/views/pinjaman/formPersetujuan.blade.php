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
        <h5>FORMULIR PINJAMAN <br> (JANGKA PANJANG, JANGKA PENDEK)**</h5>
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
                <td></td>
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
                <td id="gaji"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script>
    // var saldo = besarPinjaman = maksimalPinjaman = biayaAdministrasi = provisi = asuransi = jasa = angsuranPokok = besarAngsuran = 0;
    var gaji = {{ $anggota->penghasilan->gaji_bulanan }};
    $('#gaji').text(toRupiah(gaji));
    function toRupiah(number)
    {
        var stringNumber = number.toString();
        var length = stringNumber.length;
        var temp = length;
        var res = "Rp ";
        for (let i = 0; i < length; i++) {
            res = res + stringNumber.charAt(i);
            temp--;
            if (temp%3 == 0 && temp > 0)
            {
                res = res + ".";
            }
        }
        return res;
    }
</script>