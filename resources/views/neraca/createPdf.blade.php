<html>
<head>

    
<style>
    .text-center{
        text-align: center;
    }

    t

    .border-collapse, .border-collapse tr td, .border-collapse tr th {
        border: 1px solid black;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td{
        vertical-align: top;
        font-family: Century Gothic, CenturyGothic, AppleGothic, sans-serif; font-size: 9px;
    
    }
    td.rupiah{
        text-align: right;
    }

    body{
        font-family: Century Gothic, CenturyGothic, AppleGothic, sans-serif; font-size: 10px;
    }

    h5{
        font-family: Century Gothic, CenturyGothic, AppleGothic, sans-serif; font-size: 14px;
    }
    .page-break {
        page-break-after: always;
    }
    @page {
        margin-top: 5px;
    }
</style>
        </head>

<body>
    <main>
    <table>
        <tr>
            <td style="width:17%; text-align: left">Koperasi Pegawai Maritim</td>
            <td></td>
            <td style="width:15%; text-align: left">Rep. ID : Neraca</td>
        </tr>
        <tr>
            <td style="text-align: left">JAKARTA</td>
            <td style="font-size: 16px"><b>N  E  R  A  C  A</b></td>
            <td style="text-align: left">Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td style="text-align: left">Unit Simpan/Pinjam</td>
            <td>Bulan : {{ strtoupper(\Carbon\Carbon::createFromFormat('m-Y', $period)->locale('id_ID')->isoFormat('MMMM Y')) }}</td>
            <td style="text-align: left">Halaman : 1/1</td>
        </tr>
    </table>
    <table >
        <tr>
            <td syle="vertical-align:top"><table class="border-collapse" >
                    <thead>
                       <tr>
                        <th colspan="5">AKTIVA</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>Rek</th>
                            <th>Nama Rekening</th>
                            <th >Bulan ini</th>
                            <th >Bulan lalu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>I</td>
                            <td colspan="4">AKTIVA LANCAR</td>
                            
                        </tr>
                        @foreach ($aktivalancar as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                                <td style="text-align:right;">{{number_format(number_format($item['saldo'], 2, ",", ".") , 2, ",", ".") }}</td>
                                <td style="text-align:right;">{{number_format(number_format($item['saldoLastMonth'], 2, ",", ".") , 2, ",", ".") }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Jumlah Aktiva Lancar</td>
                            <td style="text-align:right;">{{number_format($aktivalancar->sum('saldo') , 2, ",", ".")}}</td>
                            <td style="text-align:right;">{{number_format($aktivalancar->sum('saldoLastMonth'), 2, ",", ".")  }}</td>
                        </tr>
                        <tr>
                            <td>II</td>
                            <td colspan="4">AKTIVA TETAP</td>
                           
                        </tr>
                        @foreach ($aktivatetap as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                                <td style="text-align:right;">{{number_format($item['saldo'], 2, ",", ".")  }}</td>
                                <td style="text-align:right;">{{number_format($item['saldoLastMonth'], 2, ",", ".")  }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Jumlah Aktiva Tetap</td>
                            <td style="text-align:right;">{{number_format($aktivatetap->sum('saldo') , 2, ",", ".") }}</td>
                            <td style="text-align:right;">{{number_format($aktivatetap->sum('saldoLastMonth'), 2, ",", ".")  }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>TOTAL AKTIVA</td>
                            <td style="text-align:right;">{{number_format($aktivatetap->sum('saldo')+$aktivalancar->sum('saldo') , 2, ",", ".") }}</td>
                            <td style="text-align:right;">{{number_format($aktivatetap->sum('saldoLastMonth')+$aktivalancar->sum('saldoLastMonth') , 2, ",", ".") }}</td>
                        </tr>
                    </tbody>
                </table>
                </td>
            <td>
                <table class="border-collapse" >
                    <thead>
                       <tr>
                                <th colspan="5">PASIVA</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>Rek</th>
                            <th>Nama Rekening</th>
                            <th >Bulan ini</th>
                            <th >Bulan lalu</th>
                        </tr>
                    </thead>
                    <tbody>
                            
                        <tr>
                            <td>III</td>
                            <td colspan="4">KEWAJIBAN LANCAR</td>
                            
                        </tr>
                        @foreach ($kewajibanlancar as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                                <td style="text-align:right;">{{number_format($item['saldo'], 2, ",", ".")  }}</td>
                                <td style="text-align:right;">{{number_format($item['saldoLastMonth'], 2, ",", ".")  }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Jumlah Kewajiban Lancar</td>
                            <td style="text-align:right;">{{number_format($kewajibanlancar->sum('saldo') , 2, ",", ".") }}</td>
                            <td style="text-align:right;">{{number_format($kewajibanlancar->sum('saldoLastMonth'), 2, ",", ".")  }}</td>
                        </tr>
                        <tr>
                            <td>IV</td>
                            <td colspan="4">KEWAJIBAN JANGKA PANJANG</td>
                            
                        </tr>
                        @foreach ($kewajibanjangkapanjang as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                                <td style="text-align:right;">{{number_format($item['saldo'], 2, ",", ".")  }}</td>
                                <td style="text-align:right;">{{number_format($item['saldoLastMonth'], 2, ",", ".")  }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Jumlah Kewajiban Jangka Panjang</td>
                            <td style="text-align:right;">{{number_format($kewajibanjangkapanjang->sum('saldo'), 2, ",", ".")  }}</td>
                            <td style="text-align:right;">{{number_format($kewajibanjangkapanjang->sum('saldoLastMonth'), 2, ",", ".")  }}</td>
                        </tr>
                        <tr>
                            <td>V</td>
                            <td colspan="4">KEKAYAAN BERSIH</td>
                           
                        </tr>
                        @foreach ($kekayaanbersih as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                                <td style="text-align:right;">{{number_format($item['saldo'], 2, ",", ".")  }}</td>
                                <td style="text-align:right;">{{number_format($item['saldoLastMonth'], 2, ",", ".")  }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Jumlah Kekayaan Bersih</td>
                            <td style="text-align:right;">{{number_format($kekayaanbersih->sum('saldo'), 2, ",", ".")  }}</td>
                            <td style="text-align:right;">{{number_format($kekayaanbersih->sum('saldoLastMonth') , 2, ",", ".") }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>TOTAL PASIVA</td>
                            <td style="text-align:right;">{{number_format($kekayaanbersih->sum('saldo')+$kewajibanjangkapanjang->sum('saldo')+$kewajibanlancar->sum('saldo') , 2, ",", ".") }}</td>
                            <td style="text-align:right;">{{number_format($kekayaanbersih->sum('saldoLastMonth')+$kewajibanjangkapanjang->sum('saldoLastMonth')+$kewajibanlancar->sum('saldoLastMonth') , 2, ",", ".") }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    
                    

    <br>
    <table>
        <tr>
            <td colspan="6" style="text-align: center">JAKARTA, {{ strtoupper(\Carbon\Carbon::createFromFormat('m-Y', $period)->locale('id_ID')->isoFormat('MMMM Y')) }}</td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center">PENGURUS KOPEPASI PEGAWAI MARITIM TANJUNG PRIOK</td>
        </tr>
        <tr>
            <td style="width: 20%"></td>
            <td style="text-align: center">
                KETUA<br><br><br><br><br>
                DJUSMAN HI UMAR
            </td>
            <td style="text-align: center">
                WAKIL KETUA<br><br><br><br><br>
                M. FAJAR SUHARDIMAN
            </td>
            <td style="text-align: center">
                SEKRETARIS<br><br><br><br><br>
                AINUL
            </td>
            <td style="text-align: center">
                BENDAHARA<br><br><br><br><br>
                ARDIANSYAH
            </td>
            <td style="width: 20%"></td>
        </tr>
    </table>

    </main>
<script type="text/php">
    if (isset($pdf)) {
        $text = "Halaman {PAGE_NUM} / {PAGE_COUNT}";
        $size = 6;
        $font = $fontMetrics->getFont("helvetica");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>

</body>
</html>
    